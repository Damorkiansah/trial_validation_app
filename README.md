# Trial Validation System PHP + MySQL v4

## Instalasi
Requirement: PHP 7.4.30 atau lebih baru, MySQL/MariaDB, dan ekstensi PDO MySQL aktif.

1. Extract folder `trial_validation_app` ke `C:\xampp\htdocs\trial_validation_app`.
2. Buka phpMyAdmin, import file: `database/trial_validation_system_mysql.sql`.
3. Untuk testing cepat, jalankan PHP built-in server:
   ```powershell
   cd C:\xampp\htdocs\trial_validation_app
   C:\xampp\php\php.exe -S localhost:8000 -t public
   ```
4. Buka `http://localhost:8000`.

## Deploy on-prem dengan XAMPP Apache
Folder project sudah dilengkapi `.htaccess` agar bisa dijalankan dari subfolder XAMPP tanpa ubah VirtualHost. Jika Apache XAMPP sudah mengizinkan `.htaccess`, cukup buka:

```text
http://IP-SERVER/trial_validation_app/
```

Contoh:

```text
http://192.168.1.10/trial_validation_app/
```

Jika masih tampil folder, berarti `.htaccess` belum aktif di Apache. Untuk akses cepat tanpa ubah Apache, buka:

```text
http://IP-SERVER/trial_validation_app/public/
```

Namun URL yang paling rapi tetap memakai document root ke folder `public`.

Rekomendasi:

1. Buka file Apache VirtualHost:
   ```text
   C:\xampp\apache\conf\extra\httpd-vhosts.conf
   ```
2. Tambahkan konfigurasi:
   ```apache
   <VirtualHost *:80>
       ServerName trial-validation.local
       DocumentRoot "C:/xampp/htdocs/trial_validation_app/public"

       <Directory "C:/xampp/htdocs/trial_validation_app/public">
           AllowOverride All
           Require all granted
           DirectoryIndex index.php
       </Directory>
   </VirtualHost>
   ```
3. Pastikan file ini aktif di `C:\xampp\apache\conf\httpd.conf`:
   ```apache
   Include conf/extra/httpd-vhosts.conf
   ```
4. Tambahkan host di server dan PC user yang akan akses:
   ```text
   192.168.1.10 trial-validation.local
   ```
   Ganti `192.168.1.10` dengan IP server on-prem.
5. Restart Apache dari XAMPP Control Panel.
6. Buka:
   ```text
   http://trial-validation.local
   ```

Jika tidak ingin memakai domain lokal, akses via IP juga bisa, asalkan VirtualHost/default site Apache diarahkan ke `C:/xampp/htdocs/trial_validation_app/public`.

## Login default
- Admin: admin@local.test / admin123
- Staff: staff@local.test / staff123
- PRD: prd@local.test / prd123
- RNI: rni@local.test / rni123
- QAC: qac@local.test / qac123
- PRNI: prni@local.test / prni123
- PI: pi@local.test / pi123
- Manager QAC: manager.qac@local.test / manager123

## Catatan perubahan v4
- Password login tersembunyi dan disimpan hash.
- Password user hanya bisa diubah Admin dari menu Users.
- Staff dan Admin bisa tambah parameter; reviewer tidak bisa.
- Reviewer/Manager tidak melihat menu New Trial.
- Product Name dari database Product dengan Select2 search, FG Code otomatis.
- Product Type menentukan parameter validation.
- Risk Level: Low, Medium, High.
- Estimate Qty angka saja.
- Validasi field wajib tidak mereset data input.
- Decision OK/NOT OK/N/A sesuai rule.
- Weighing Packaging dan Filling dinamis: minimal 1 sample jika tidak Skip.
- Summary menampilkan semua timbang dan semua foto per kategori.

## Upgrade hardening
Jika database sudah pernah di-import sebelum update hardening, jalankan file:

```sql
database/20260506_hardening_migration.sql
database/20260506_add_trial_header_fields.sql
database/20260506_normalize_reviewer_departments.sql
```

Perubahan utama:
- Semua form POST memakai CSRF token.
- Submit review berubah menjadi POST dan dicek kelengkapan datanya.
- Review memiliki `review_round`, sehingga revisi tidak menimpa riwayat review lama.
- Approval Manager QAC memakai password e-signature dan comment wajib.
- Upload foto divalidasi MIME, ukuran maksimum 10 MB, dan nama file dibuat acak.
- Audit trail disimpan di tabel `audit_logs`.
- Header trial memiliki field tambahan: Batch Number, Bulk Code, Support Team, Initiated person/team, Reason, dan B.O.M.
- Department user reviewer dinormalisasi agar role PRD/RNI/QAC/PRNI/PI selalu bisa melihat review department sebelumnya.
