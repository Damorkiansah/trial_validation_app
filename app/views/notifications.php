<div class="page-heading">
  <div>
    <h1>Notifications</h1>
    <p>Daftar notifikasi internal yang membutuhkan perhatian Anda.</p>
  </div>
  <form method="post" action="/notifications/read-all">
    <?php csrf_field(); ?>
    <button class="btn btn-light">Mark All as Read</button>
  </form>
</div>

<div class="card notification-filter">
  <a class="btn <?=$filter==='all'?'btn-primary':'btn-light'?>" href="/notifications?filter=all">All</a>
  <a class="btn <?=$filter==='unread'?'btn-primary':'btn-light'?>" href="/notifications?filter=unread">Unread</a>
  <a class="btn <?=$filter==='read'?'btn-primary':'btn-light'?>" href="/notifications?filter=read">Read</a>
</div>

<section class="card table-card">
  <div class="table-wrap">
    <table class="modern-table">
      <tr>
        <th>Title</th>
        <th>Message</th>
        <th>Related Trial</th>
        <th>Type</th>
        <th>Created At</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php foreach($items as $n): ?>
        <tr class="<?=$n['is_read']?'':'notification-row-unread'?>">
          <td><?=h($n['title'])?></td>
          <td><?=h($n['message'])?></td>
          <td><?=!empty($n['trial_code'])?'<a href="/trials/'.h($n['trial_id']).'/report">'.h($n['trial_code']).'</a>':'-'?></td>
          <td><span class="type-badge <?=h(notification_type_class($n['type']))?>"><?=h($n['type'])?></span></td>
          <td><?=h($n['created_at'])?></td>
          <td><?=((int)$n['is_read']===1)?'<span class="badge status-muted">Read</span>':'<span class="badge status-review">Unread</span>'?></td>
          <td class="actions">
            <a class="btn btn-primary" href="/notifications/read/<?=$n['id']?>"><?=((int)$n['is_read']===1)?'Open':'Mark as Read'?></a>
            <form method="post" action="/notifications/remove/<?=$n['id']?>" class="inline-form" onsubmit="return confirm('Remove this notification from your view?')">
              <?php csrf_field(); ?>
              <button class="btn btn-light">Remove</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="7" class="empty-state">Belum ada notifikasi.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
