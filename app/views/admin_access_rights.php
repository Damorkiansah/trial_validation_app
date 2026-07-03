<div class="page-heading">
  <div>
    <h1>Hak Akses</h1>
    <p>Menu khusus Super Admin untuk membuat kategori hak akses dan menetapkan role user.</p>
  </div>
</div>

<section class="card table-card">
  <h3>Kategori Hak Akses</h3>
  <form method="post" action="/admin/users/role-categories/save" class="grid">
    <?php csrf_field(); ?>
    <input name="name" placeholder="Nama kategori hak akses" required>
    <input name="sort_order" type="number" placeholder="Urutan" value="0">
    <button>Tambah Kategori</button>
  </form>
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Kategori Custom</th><th>Urutan</th><th>Action</th></tr>
      <?php foreach(($customRoleCategories??[]) as $category): ?>
        <tr>
          <td><?=h($category['name'])?></td>
          <td><?=h($category['sort_order'])?></td>
          <td class="actions">
            <form method="post" action="/admin/users/role-categories/<?=(int)$category['id']?>/delete" class="inline-form" onsubmit="return confirm('Delete this access category?')">
              <?php csrf_field(); ?>
              <button class="danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(empty($customRoleCategories)): ?><tr><td colspan="3" class="empty-state">Belum ada kategori hak akses custom.</td></tr><?php endif; ?>
    </table>
  </div>
</section>

<section class="card table-card">
  <h3>Kategori Reviewer</h3>
  <p class="muted">Kategori ini dipakai saat Staff memilih department review, notifikasi reviewer, dan laporan Department Review.</p>
  <form method="post" action="/admin/access-rights/reviewer-departments/save" class="grid">
    <?php csrf_field(); ?>
    <input name="name" placeholder="Contoh: QA, RND, PACKAGING" required>
    <input name="sort_order" type="number" placeholder="Urutan" value="0">
    <button>Tambah Reviewer</button>
  </form>
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Kategori Reviewer Custom</th><th>Urutan</th><th>Action</th></tr>
      <?php foreach(($customReviewerDepartments??[]) as $category): ?>
        <tr>
          <td><?=h($category['name'])?></td>
          <td><?=h($category['sort_order'])?></td>
          <td class="actions">
            <form method="post" action="/admin/access-rights/reviewer-departments/<?=(int)$category['id']?>/delete" class="inline-form" onsubmit="return confirm('Delete this reviewer category?')">
              <?php csrf_field(); ?>
              <button class="danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(empty($customReviewerDepartments)): ?><tr><td colspan="3" class="empty-state">Belum ada kategori reviewer custom.</td></tr><?php endif; ?>
    </table>
  </div>
  <p class="muted">Reviewer aktif: <?=h(implode(', ', $reviewerDepartments??reviewer_department_codes()))?></p>
</section>

<section class="card table-card">
  <h3>Permission Edit Draft Report</h3>
  <p class="muted">Draft report hanya bisa dilihat dan diedit oleh owner. Super Admin dapat memberi izin edit ke Staff lain dari form ini.</p>
  <?php if(empty($permissionTableReady)): ?>
    <div class="flash">Tabel permission belum tersedia. Jalankan migration <code>database/20260702_trial_edit_permissions.sql</code>.</div>
  <?php else: ?>
    <form method="post" action="/admin/access-rights/draft-permissions/grant" class="grid">
      <?php csrf_field(); ?>
      <label>Draft Report
        <select name="trial_id" class="select2" required>
          <option value="">Pilih Draft Report</option>
          <?php foreach(($draftTrials??[]) as $draft): ?>
            <option value="<?=(int)$draft['id']?>"><?=h($draft['trial_code'].' - '.$draft['product_name'].' (owner: '.$draft['created_by'].')')?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>User Staff
        <select name="user_id" class="select2" required>
          <option value="">Pilih User Staff</option>
          <?php foreach(($staffUsers??[]) as $staff): ?>
            <option value="<?=(int)$staff['id']?>"><?=h(($staff['name']?:$staff['email']).' - '.$staff['email'])?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button>Berikan Izin Edit</button>
    </form>
    <div class="table-wrap">
      <table class="modern-table">
        <tr><th>Trial</th><th>Owner</th><th>Diberikan Ke</th><th>Diberikan Oleh</th><th>Waktu</th><th>Action</th></tr>
        <?php foreach(($draftPermissions??[]) as $permission): ?>
          <tr>
            <td><?=h($permission['trial_code'].' - '.$permission['product_name'])?></td>
            <td><?=h($permission['owner_email'])?></td>
            <td><?=h(($permission['user_name']?:$permission['user_email']).' - '.$permission['user_email'])?></td>
            <td><?=h($permission['granted_by_name']?:$permission['granted_by_email'])?></td>
            <td><?=h($permission['granted_at'])?></td>
            <td class="actions">
              <form method="post" action="/admin/access-rights/draft-permissions/<?=(int)$permission['id']?>/revoke" class="inline-form" onsubmit="return confirm('Cabut izin edit Draft report ini?')">
                <?php csrf_field(); ?>
                <button class="danger">Revoke</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($draftPermissions)): ?><tr><td colspan="6" class="empty-state">Belum ada izin edit Draft report aktif.</td></tr><?php endif; ?>
      </table>
    </div>
  <?php endif; ?>
</section>

<section class="card table-card">
  <form method="get" action="/admin/access-rights" class="dashboard-filter">
    <label>Search User
      <input name="q" value="<?=h($filters['q']??'')?>" placeholder="Nama, email, role, department">
    </label>
    <div class="filter-actions">
      <button class="btn btn-primary">Search</button>
      <a class="btn btn-light" href="/admin/access-rights">Reset</a>
    </div>
  </form>
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Name</th><th>Email</th><th>Role Saat Ini</th><th>Dept</th><th>Ubah Hak Akses</th></tr>
      <?php foreach($users as $usr): ?>
        <tr>
          <td><?=h($usr['name'])?></td>
          <td><?=h($usr['email'])?></td>
          <td><?=h($usr['role'])?></td>
          <td><?=h($usr['department'])?></td>
          <td class="actions">
            <?php if((int)$usr['id']===(int)(u()['id']??0)): ?>
              <span class="muted">Akun aktif</span>
            <?php else: ?>
              <form method="post" action="/admin/access-rights/users/<?=(int)$usr['id']?>/role" class="inline-form">
                <?php csrf_field(); ?>
                <select name="role">
                  <?php foreach(($roleCategories??role_categories()) as $roleCategory): ?>
                    <option value="<?=h($roleCategory)?>" <?=$usr['role']===$roleCategory?'selected':''?>><?=h($roleCategory)?></option>
                  <?php endforeach; ?>
                </select>
                <input name="department" value="<?=h($usr['department'])?>" placeholder="Department">
                <button>Update</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$users): ?><tr><td colspan="5" class="empty-state">Tidak ada user.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
