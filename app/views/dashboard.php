<div class="page-heading">
  <div>
    <h1>Trial Dashboard</h1>
    <p>Kelola dan pantau proses trial validation</p>
  </div>
  <?php if(is_admin()||role()==='Staff'): ?>
    <a class="btn btn-primary" href="/trials/create">New Trial</a>
  <?php endif; ?>
</div>

<section class="summary-grid">
  <a class="summary-card" href="/dashboard">
    <span>Total Trials</span>
    <strong><?=h($summary['total']??0)?></strong>
  </a>
  <a class="summary-card" href="/dashboard?status=Draft">
    <span>Draft</span>
    <strong><?=h($summary['draft']??0)?></strong>
  </a>
  <a class="summary-card" href="/trials/in-review">
    <span>In Review</span>
    <strong><?=h($summary['in_review']??0)?></strong>
  </a>
  <a class="summary-card" href="/trials/waiting-approval">
    <span>Ready for Approval</span>
    <strong><?=h($summary['ready']??0)?></strong>
  </a>
  <a class="summary-card" href="/trials/approved">
    <span>Approved</span>
    <strong><?=h($summary['approved']??0)?></strong>
  </a>
  <a class="summary-card" href="/trials/need-revision">
    <span>Need Revision</span>
    <strong><?=h($summary['need_revision']??0)?></strong>
  </a>
  <a class="summary-card" href="/trials/rejected">
    <span>Rejected</span>
    <strong><?=h($summary['rejected']??0)?></strong>
  </a>
</section>

<form method="get" action="/dashboard" class="card dashboard-filter">
  <label>Search
    <input name="q" value="<?=h($filters['q']??'')?>" placeholder="Trial, product, FG code, category, scope, machine">
  </label>
  <label>Status
    <select name="status">
      <option value="">Semua status</option>
      <?php foreach(['Draft','In Review','Ready for Approval','Approved','Need Revision','Rejected'] as $status): ?>
        <option value="<?=h($status)?>" <?=($filters['status']??'')===$status?'selected':''?>><?=h($status)?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Product Type
    <select name="product_type">
      <option value="">Semua kategori</option>
      <?php foreach($productTypes as $type): ?>
        <option value="<?=h($type['name'])?>" <?=($filters['product_type']??'')===$type['name']?'selected':''?>><?=h($type['name'])?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Tanggal Dari
    <input type="date" name="date_from" value="<?=h($filters['date_from']??'')?>">
  </label>
  <label>Tanggal Sampai
    <input type="date" name="date_to" value="<?=h($filters['date_to']??'')?>">
  </label>
  <div class="filter-actions">
    <button class="btn btn-primary">Search</button>
    <a class="btn btn-light" href="/dashboard">Reset</a>
  </div>
</form>

<section class="card table-card">
  <div class="table-header">
    <h2>Trial List</h2>
    <span><?=count($trials)?> trial ditemukan</span>
  </div>
  <div class="table-wrap">
    <table class="modern-table">
      <tr>
        <th>Trial ID</th>
        <th>Product Name</th>
        <th>Finish Good Code</th>
        <th>Product Type</th>
        <th>Status</th>
        <th>Current Step</th>
        <th>Created Date</th>
        <th>Pending With</th>
        <th>Action</th>
      </tr>
      <?php foreach($trials as $t): ?>
        <tr>
          <td><?=h($t['trial_code'])?></td>
          <td><?=h($t['product_name'])?></td>
          <td><?=h($t['finish_good_code'])?></td>
          <td><?=h($t['product_type'])?></td>
          <td><?=status_badge($t['progress_status'],$t['final_decision']??'')?></td>
          <td><?=h($t['current_step'])?></td>
          <td><?=h($t['created_at'])?></td>
          <td><?=h($t['pending_with'])?></td>
          <td class="actions"><?=trial_action_html($t)?></td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$trials): ?>
        <tr><td colspan="9" class="empty-state">Tidak ada trial untuk filter ini.</td></tr>
      <?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
