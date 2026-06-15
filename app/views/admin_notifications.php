<div class="page-heading">
  <div>
    <h1>Admin Notifications</h1>
    <p>Kontrol semua notification dan delete permanen jika diperlukan.</p>
  </div>
</div>

<section class="card table-card">
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>ID</th><th>Title</th><th>Target</th><th>Trial</th><th>Type</th><th>Created At</th><th>Action</th></tr>
      <?php foreach($items as $n): ?>
        <tr>
          <td><?=h($n['id'])?></td>
          <td><?=h($n['title'])?><br><small class="muted"><?=h($n['message'])?></small></td>
          <td><?=h($n['user_name']?:($n['role_target']?:'-'))?> <?=h($n['department_target']?:'')?></td>
          <td><?=h($n['trial_code']??'-')?></td>
          <td><span class="type-badge <?=h(notification_type_class($n['type']))?>"><?=h($n['type'])?></span></td>
          <td><?=h($n['created_at'])?></td>
          <td>
            <form method="post" action="/admin/notifications/delete/<?=$n['id']?>" class="inline-form" onsubmit="return confirm('Delete this notification permanently?')">
              <?php csrf_field(); ?>
              <button class="danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="7" class="empty-state">Belum ada notification.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
