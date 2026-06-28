<div class="page-heading">
  <div>
    <h1><?=h($pageTitle)?></h1>
    <p><?=h($pageSubtitle)?></p>
  </div>
</div>

<form method="get" class="card dashboard-filter">
  <label>Search
    <input name="q" value="<?=h($filters['q']??'')?>" placeholder="Trial, product, FG code, scope, machine">
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
    <a class="btn btn-light" href="<?=h($currentPath??normalize_request_path(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)))?>">Reset</a>
  </div>
</form>

<section class="card table-card">
  <div class="table-header">
    <h2><?=h($pageTitle)?></h2>
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
        <tr><td colspan="9" class="empty-state">Tidak ada trial pada halaman ini.</td></tr>
      <?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
