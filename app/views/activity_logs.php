<div class="page-heading">
  <div>
    <h1>Activity Logs</h1>
    <p>Riwayat action penting pada aplikasi.</p>
  </div>
</div>

<form method="get" class="card dashboard-filter">
  <label>Date From <input type="date" name="date_from" value="<?=h($filters['date_from']??'')?>"></label>
  <label>Date To <input type="date" name="date_to" value="<?=h($filters['date_to']??'')?>"></label>
  <label>User <input name="user" value="<?=h($filters['user']??'')?>" placeholder="User"></label>
  <label>Role <input name="role" value="<?=h($filters['role']??'')?>" placeholder="Role"></label>
  <label>Module <input name="module" value="<?=h($filters['module']??'')?>" placeholder="Module"></label>
  <label>Action <input name="action" value="<?=h($filters['action']??'')?>" placeholder="Action"></label>
  <label>Search <input name="q" value="<?=h($filters['q']??'')?>" placeholder="Keyword"></label>
  <div class="filter-actions">
    <button class="btn btn-primary">Filter</button>
    <a class="btn btn-light" href="/admin/activity-logs">Reset</a>
  </div>
</form>

<section class="card table-card">
  <form method="post" action="/admin/activity-logs/delete-selected" onsubmit="return confirm('Delete selected activity logs permanently?')">
    <?php csrf_field(); ?>
    <div class="table-header">
      <h2>Activity Log Data</h2>
      <button class="danger" id="bulk-delete-logs" disabled>Delete Selected</button>
    </div>
    <div class="table-wrap">
      <table class="modern-table">
      <tr>
        <th><input type="checkbox" id="select-all-logs" aria-label="Select all activity logs"></th>
        <th>Date/Time</th><th>User</th><th>Role</th><th>Action</th><th>Module</th><th>Record</th><th>IP Address</th><th>Action</th>
      </tr>
      <?php foreach($items as $log): ?>
        <tr>
          <td><input type="checkbox" class="log-checkbox" name="log_ids[]" value="<?=h($log['id'])?>" aria-label="Select activity log"></td>
          <td><?=h($log['created_at'])?></td>
          <td><?=h($log['user_name'])?></td>
          <td><?=h($log['user_role'])?></td>
          <td><?=h($log['action'])?></td>
          <td><?=h($log['module'])?></td>
          <td><?=h($log['record_label']?:$log['record_id'])?></td>
          <td><?=h($log['ip_address'])?></td>
          <td class="actions">
            <button type="button" class="btn btn-light" onclick="document.getElementById('log-<?=$log['id']?>').classList.add('show')">Detail</button>
            <form method="post" action="/admin/activity-logs/delete/<?=$log['id']?>" class="inline-form" onsubmit="return confirm('Delete this activity log permanently?')">
              <?php csrf_field(); ?>
              <button class="danger">Delete</button>
            </form>
          </td>
        </tr>
        <tr id="log-<?=$log['id']?>" class="log-detail-row">
          <td colspan="9">
            <div class="log-detail-panel">
              <button type="button" class="btn btn-light" onclick="document.getElementById('log-<?=$log['id']?>').classList.remove('show')">Close</button>
              <h3>Old Data</h3>
              <pre><?=h($log['old_data']?:'null')?></pre>
              <h3>New Data</h3>
              <pre><?=h($log['new_data']?:'null')?></pre>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="9" class="empty-state">Belum ada activity log.</td></tr><?php endif; ?>
      </table>
    </div>
  </form>
  <?php render_pagination($pagination??null); ?>
</section>

<script>
(function(){
  const selectAll=document.getElementById('select-all-logs');
  const checks=Array.from(document.querySelectorAll('.log-checkbox'));
  const bulk=document.getElementById('bulk-delete-logs');
  function update(){
    const selected=checks.filter(c=>c.checked).length;
    if(bulk) bulk.disabled=selected===0;
    if(selectAll) selectAll.checked=checks.length>0&&selected===checks.length;
  }
  if(selectAll) selectAll.addEventListener('change',function(){
    checks.forEach(c=>c.checked=selectAll.checked);
    update();
  });
  checks.forEach(c=>c.addEventListener('change',update));
  update();
})();
</script>
