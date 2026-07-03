<div class="page-heading">
  <div>
    <h1>Users</h1>
    <p>Manage user account dan role akses aplikasi.</p>
  </div>
</div>

<form method="post" action="/admin/users/save" class="card grid">
  <?php csrf_field(); ?>
  <input name="name" placeholder="Name" required>
  <input name="email" type="email" placeholder="Email" required>
  <input name="password" type="password" placeholder="New Password" required>
  <select name="role">
    <?php foreach(($roleCategories??role_categories()) as $roleCategory): ?>
      <option value="<?=h($roleCategory)?>"><?=h($roleCategory)?></option>
    <?php endforeach; ?>
  </select>
  <label>Department
    <input name="department" placeholder="Auto untuk role reviewer">
    <small class="muted">Untuk role reviewer, department otomatis disamakan dengan role.</small>
  </label>
  <button>Save / Change Password</button>
</form>

<section class="card table-card">
  <form method="get" action="/settings/users" class="dashboard-filter">
    <label>Search User
      <input name="q" value="<?=h($filters['q']??'')?>" placeholder="Nama, email, role, department">
    </label>
    <div class="filter-actions">
      <button class="btn btn-primary">Search</button>
      <a class="btn btn-light" href="/settings/users">Reset</a>
    </div>
  </form>
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Name</th><th>Email</th><th>Role</th><th>Dept</th><th>Action</th></tr>
      <?php foreach($users as $usr): ?>
        <tr>
          <td><?=h($usr['name'])?></td>
          <td><?=h($usr['email'])?></td>
          <td><?=h($usr['role'])?></td>
          <td><?=h($usr['department'])?></td>
          <td class="actions">
            <?php if((int)$usr['id']!==(int)(u()['id']??0)&&($usr['role']!=='Super Admin'||is_super_admin())): ?>
              <form method="post" action="/admin/users/<?=$usr['id']?>/delete" class="inline-form" onsubmit="return confirm('Delete this user account?')">
                <?php csrf_field(); ?>
                <button class="danger">Delete</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$users): ?><tr><td colspan="5" class="empty-state">Belum ada user.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
