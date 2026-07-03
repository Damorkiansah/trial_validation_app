<div class="page-heading">
  <div>
    <h1>Trial Summary Report</h1>
    <p>Ringkasan semua trial validation.</p>
  </div>
  <button class="btn btn-light" onclick="window.print()">Print</button>
</div>

<form method="get" class="card dashboard-filter">
  <label>Date From<input type="date" name="date_from" value="<?=h($filters['date_from']??'')?>"></label>
  <label>Date To<input type="date" name="date_to" value="<?=h($filters['date_to']??'')?>"></label>
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
      <option value="">Semua product type</option>
      <?php foreach($productTypes as $type): ?>
        <option value="<?=h($type['name'])?>" <?=($filters['product_type']??'')===$type['name']?'selected':''?>><?=h($type['name'])?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Validation Scope
    <select name="validation_scope">
      <option value="">Semua scope</option>
      <?php foreach($validationScopes as $scope): ?>
        <option value="<?=h($scope['name'])?>" <?=($filters['validation_scope']??'')===$scope['name']?'selected':''?>><?=h($scope['name'])?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Machine Used
    <select name="machine_used">
      <option value="">Semua machine</option>
      <?php foreach($machines as $machine): ?>
        <option value="<?=h($machine['name'])?>" <?=($filters['machine_used']??'')===$machine['name']?'selected':''?>><?=h($machine['name'])?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Product Name<input name="product_name" value="<?=h($filters['product_name']??'')?>" placeholder="Product name"></label>
  <div class="filter-actions"><button class="btn btn-primary">Search</button><a class="btn btn-light" href="/report/trial-summary">Reset</a></div>
</form>

<section class="card table-card print-card">
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Trial ID</th><th>Product Name</th><th>Finish Good Code</th><th>Product Type</th><th>Validation Scope</th><th>Machine Used</th><th>Status</th><th>Current Step</th><th>Created By</th><th>Created Date</th><th>Pending With</th><th>Action</th></tr>
      <?php foreach($items as $t): ?>
        <tr>
          <td><?=h($t['trial_code'])?></td>
          <td><?=h($t['product_name'])?></td>
          <td><?=h($t['finish_good_code'])?></td>
          <td><?=h($t['product_type'])?></td>
          <td><?=h(display_multi_value($t['validation_scope']))?></td>
          <td><?=h(display_multi_value($t['machine_used']))?></td>
          <td><?=status_badge($t['progress_status'],$t['final_decision']??'')?></td>
          <td><?=h($t['current_step'])?></td>
          <td><?=h($t['created_by'])?></td>
          <td><?=h($t['created_at'])?></td>
          <td><?=h($t['pending_with'])?></td>
          <td>
            <a class="btn btn-primary" href="/trials/<?=$t['id']?>/report">View Summary</a>
            <button class="btn btn-light" onclick="window.print()">Print</button>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="12" class="empty-state">Tidak ada data trial.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
