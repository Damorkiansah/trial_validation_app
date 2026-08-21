# Migration Plan — Trial Validation System → Laravel + React

Status: **Draft untuk diskusi**. Belum ada implementasi dimulai.
Terakhir diperbarui: 2026-08-21

## 1. Latar belakang & tujuan

Aplikasi saat ini adalah plain PHP (tanpa framework): satu `public/index.php` (~1500 baris) sebagai router + controller + business logic, ditambah `app/bootstrap.php` (helper functions global) dan 26 file view di `app/views/`. Auth pakai native PHP session, database MySQL diakses langsung via PDO. Total ~4700 baris kode, deployment on-prem via XAMPP/Apache di jaringan intranet.

Alasan migrasi:
- **Maintainability** — logic menumpuk di satu file besar, susah dikembangkan dan di-debug.
- **UI/UX modern** — ingin pengalaman SPA yang lebih interaktif, tanpa reload halaman penuh.
- **Rencana ekspansi** — akan ada mobile app / integrasi sistem eksternal yang butuh API terpisah, dikerjakan **setelah** web migration selesai.

## 2. Target arsitektur

- **Server:** app baru di-setup di **server baru, Ubuntu 26**, terpisah fisik dari server lama (yang saat ini menjalankan app PHP existing via XAMPP/Windows). Stack yang disarankan di Ubuntu: **Nginx + PHP-FPM** (lebih standar untuk Laravel di Linux dibanding Apache).
- **Topologi jaringan:** dua server ini satu LAN/VPN internal kantor (bisa saling akses langsung via IP internal), tapi **diakses user via dua hostname/IP terpisah** — tidak ada reverse proxy tunggal yang menyatukan keduanya di depan. Konsekuensinya, app lama dan app baru adalah **origin yang berbeda** dari sudut pandang browser; navigasi antar keduanya adalah full-page redirect biasa (bukan AJAX/fetch), jadi CORS tidak relevan di sini.
- **Database:** **satu instance MySQL yang sama**, dipakai bersama oleh app lama dan app baru (wajib untuk strangler pattern — lihat §8 soal lokasi fisik DB yang masih perlu diputuskan). Karena DB shared, app baru bisa query tabel `users` dan tabel-tabel lain langsung tanpa perlu sinkronisasi data terpisah.
- **Backend:** Laravel, API-only (bukan Blade/Inertia) — dipakai bersama oleh web frontend dan mobile app nanti.
- **Auth:** Laravel Sanctum, dual-mode:
  - Web React SPA → **cookie-based SPA auth** (session cookie + CSRF), karena SPA dan API sama-sama di-serve dari server Ubuntu yang sama (same-origin) — lebih aman dari XSS dibanding Bearer token di localStorage.
  - Mobile app (nanti) → **Bearer token** (Sanctum personal access token) via endpoint login terpisah.
- **Frontend web:** React SPA (Vite), **bukan Next.js**. Alasan: aplikasi ini internal/auth-gated (bukan publik, tidak butuh SEO/SSR) — React SPA hasil build adalah file statis yang di-serve Nginx, tidak butuh proses Node.js yang harus terus menyala di server. Next.js baru relevan kalau nanti ada kebutuhan SSR publik.
- **Database:** boleh diredesain, tapi **bertahap per modul saat modul itu cutover** — bukan sekaligus di awal, supaya app lama yang masih jalan tidak mendadak rusak oleh perubahan skema.

## 3. Strategi migrasi: strangler pattern, bertahap per modul

Semua user pindah ke modul baru begitu modul itu selesai (paralel per modul, bukan per role). App lama (server existing, hostname/IP lama) dan app baru (server Ubuntu 26, hostname/IP baru) hidup berdampingan selama transisi sebagai dua alamat terpisah:

- Server lama → app PHP existing, tidak diubah, tetap di hostname/IP-nya sekarang.
- Server Ubuntu baru → Nginx meng-serve React SPA (static build) di satu path/hostname, dan reverse-proxy ke Laravel (PHP-FPM) di `/api/*`.
- Nav antar dua sistem selama transisi memakai **link/redirect biasa** ke hostname lain (mis. tombol menu di app lama yang belum dimigrasi tetap ke path lama; menu yang sudah dimigrasi link ke hostname server baru) — dibungkus mekanisme SSO bridge di §4 supaya user tidak perlu login ulang saat pindah domain.

## 4. SSO Bridge (kritis — dikerjakan di Fase 0)

Old app pakai native PHP session (`$_SESSION['user']`, file session di `storage/sessions`), password di-hash dengan `password_verify` — formatnya **kompatibel** dengan `Hash::make` bawaan Laravel (bcrypt). Artinya kedua sistem bisa share tabel `users` yang sama tanpa migrasi password.

Karena native PHP session dan Laravel session adalah dua mekanisme berbeda (tidak bisa saling baca) **dan** kedua app ada di hostname/IP berbeda (bukan same-origin, jadi cookie session juga tidak bisa saling kebaca meski formatnya sama), dipakai pola **ticket handoff sekali-pakai** lewat redirect biasa — bukan share session storage maupun panggilan API server-to-server.

Karena kedua server share **satu database MySQL yang sama** (§2), ticket cukup disimpan di satu tabel yang dibaca-tulis langsung oleh kedua sisi — tidak perlu request HTTP antar server untuk proses handoff-nya sendiri (LAN internal tetap dipakai untuk koneksi DB, bukan untuk API-to-API call di alur ini).

Tabel baru: `sso_tickets(id, token, user_id, direction, expires_at, used_at)` — token random, umur pendek (~30 detik), sekali pakai.

**Old app → New app:**
1. User klik menu yang sudah dimigrasi.
2. Old app insert row ke `sso_tickets` (query PDO langsung, tabel di DB yang sama), redirect (302, full-page, cross-origin) ke `https://<host-baru>/app/bridge?ticket=xxx`.
3. React app di server baru load, panggil `POST /api/v1/sso/exchange` dengan ticket tsb — endpoint ini di Laravel, query ke tabel `sso_tickets` yang sama.
4. Laravel verifikasi ticket (belum expired, belum used) → `Auth::login($user)` (set session cookie Laravel untuk hostname server baru) → ticket langsung ditandai used.
5. Browser sekarang sudah punya session cookie Laravel (untuk origin server baru), redirect ke halaman tujuan tanpa login ulang.

**New app → Old app:**
1. Laravel generate ticket serupa di tabel yang sama, redirect (full-page) ke `https://<host-lama>/sso/consume?ticket=xxx` (route baru kecil ditambahkan di `index.php` lama).
2. Old app verifikasi ticket ke tabel `sso_tickets` (query PDO langsung), `session_regenerate_id()`, set `$_SESSION['user']`, redirect ke path tujuan.

Karena yang lewat URL cuma ticket sekali-pakai berumur pendek (bukan token API asli), aman dari kebocoran lewat browser history/referrer/access log. Tidak ada isu CORS di alur ini karena semua perpindahan adalah full-page redirect, bukan fetch/AJAX lintas origin.

Prasyarat infra: server Ubuntu baru harus punya akses jaringan ke MySQL (port 3306) di lokasi DB berada — lihat §8 soal lokasi fisik DB yang masih perlu diputuskan.

Catatan: mekanisme ini **hanya untuk masa transisi**. Setelah Fase 4 (decommission), route `/sso/*` di kedua sisi dan tabel `sso_tickets` dihapus.

## 5. Struktur API Laravel

```
routes/api.php
  /api/v1/auth/login          (mobile — Bearer token)
  /api/v1/auth/logout
  /api/v1/auth/me
  /api/v1/sso/exchange        (web — konsumsi ticket dari old app)

  /api/v1/admin/users
  /api/v1/admin/products
  /api/v1/admin/parameters
  /api/v1/admin/access-rights
  /api/v1/admin/masters
  /api/v1/admin/notifications
  /api/v1/admin/trash
  /api/v1/activity-logs

  /api/v1/dashboard
  /api/v1/trials                          (list, show)
  /api/v1/trials/{id}/weighing
  /api/v1/trials/{id}/validation
  /api/v1/trials/{id}/reviews
  /api/v1/trials/{id}/approval
  /api/v1/trials/{id}/attachments
  /api/v1/trials/{id}/report
```

Konvensi struktur kode:
- `app/Http/Controllers/Api/V1/*Controller.php` — controller tipis, tidak berisi business logic.
- `app/Http/Requests/*` — form request class per action (validasi).
- `app/Http/Resources/*` — API Resource untuk bentuk JSON yang konsisten.
- `app/Models/*` — Eloquent model. Di awal, map langsung ke tabel existing (`protected $table = '...'`) sebelum redesign skema per modul.
- `app/Policies/*` — **konsolidasi semua fungsi `is_admin()`, `is_staff()`, `can_edit()`, `can_approve_trial()`, `can_view_trial()`, dst dari `app/bootstrap.php`** jadi Policy class per model. Ini salah satu win terbesar dari migrasi ini — logic otorisasi yang sekarang tersebar jadi satu tempat yang jelas.
- `app/Actions/*` (single-action classes) — untuk business logic alur kerja: `SubmitTrialForReview`, `ApproveTrial`, `RejectTrial`, dsb. Cocok untuk domain berbasis state machine seperti ini.

## 6. Fase migrasi

### Fase 0 — Fondasi
- Setup project Laravel (API-only) + Sanctum + React SPA (Vite) skeleton.
- Apache routing: `/` (lama), `/app` (SPA baru), `/api` (Laravel).
- Implementasi SSO bridge (lihat §4) — **harus selesai & teruji sebelum modul apa pun di-cutover**, karena semua fase berikutnya bergantung pada ini.
- Port RBAC dasar dari `bootstrap.php` ke Laravel Policies.

### Fase 1 — Modul admin/master data (risiko rendah)
Users, Products, Parameters, Access Rights, Masters, Notifications, Trash, Activity Logs. Modul berdiri sendiri (tanpa state machine approval) — cocok jadi tempat memvalidasi pola migrasi + percobaan pertama redesign skema dengan risiko kecil.

### Fase 2 — Dashboard & Trials List (read-only)
Read-heavy, low-risk. Tempat menentukan pendekatan reporting/print di stack baru sebelum masuk bagian berat.

### Fase 3 — Inti workflow trial (paling besar & berisiko, dikerjakan paling akhir)
Trial form → Weighing → Validation → Review per departemen → Approval (e-signature) → Report (approved/rejected/audit print log) → Attachments/foto.

Modul-modul ini adalah satu alur state machine yang saling terkait erat (lihat `scoped_trials_parts()`, `can_view_trial()`, `trial_completeness()` di `bootstrap.php` — logic visibility & completeness-nya cukup padat). Kemungkinan besar tidak bisa dipecah semulus Fase 1; perlu sub-tahapan sendiri yang direncanakan lebih detail saat fase ini dimulai. Perhatian ekstra dibutuhkan untuk integritas `review_round` dan `audit_logs` selama dua sistem berjalan paralel.

### Fase 4 — Decommission
- Matikan app PHP lama.
- Hapus mekanisme SSO bridge (§4) dan tabel `sso_tickets`.
- Selesaikan redesign DB yang tertunda.
- Siapkan dokumentasi API untuk proyek mobile app.

## 7. Risiko utama

1. **SSO bridge (Fase 0)** — kalau desainnya tidak matang, migrasi bisa macet di tengah jalan karena user harus login berkali-kali antar sistem.
2. **State integrity di Fase 3** — trial yang sedang dalam proses review/approval saat cutover terjadi harus tetap konsisten datanya.
3. **Redesign skema per modul** — harus disinkronkan hati-hati dengan modul mana yang masih dipakai app lama.
4. **File upload/attachment** — validasi MIME, ukuran, random filename yang sudah ada di app lama harus dipertahankan levelnya (atau lebih baik) di app baru.
5. **Print/PDF report** (`report_approved`, `report_audit_print_log`, dst) — perlu pendekatan baru di React (mis. print stylesheet atau library PDF), didesain saat Fase 2.

## 8. Belum diputuskan / parkir untuk diskusi lanjutan

- **Lokasi fisik database MySQL** — apakah tetap di server lama (server Ubuntu baru connect remote ke situ) atau dipindah/dimigrasi ke server Ubuntu baru (server lama yang connect remote, atau `config/database.php` app lama diarahkan ke host baru). Perlu dipastikan juga firewall/port 3306 terbuka antar kedua server sebelum Fase 0 mulai.
- Detail sub-tahapan Fase 3 (belum dirinci — akan direncanakan saat Fase 1 & 2 selesai dan pola migrasinya sudah stabil).
- Skema baru untuk tabel-tabel yang akan diredesain (belum dirancang).
- Struktur API/versioning untuk kebutuhan spesifik mobile app (belum relevan sampai Fase 4 selesai).
