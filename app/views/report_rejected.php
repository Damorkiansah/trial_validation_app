<div class="page-heading">
  <div>
    <h1>Rejected Report</h1>
    <p>Trial rejected atau membutuhkan revisi.</p>
  </div>
  <button class="btn btn-light" onclick="window.print()">Print</button>
</div>

<section class="card table-card print-card">
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Trial ID</th><th>Product Name</th><th>Finish Good Code</th><th>Product Type</th><th>Rejected Date</th><th>Rejected By</th><th>Reason / Final Remark</th><th>Action</th></tr>
      <?php foreach($items as $t): ?>
        <tr>
          <td><?=h($t['trial_code'])?></td>
          <td><?=h($t['product_name'])?></td>
          <td><?=h($t['finish_good_code'])?></td>
          <td><?=h($t['product_type'])?></td>
          <td><?=h($t['rejected_at']??'')?></td>
          <td><?=h(display_person_name($t['rejected_by']??''))?></td>
          <td><?=h($t['approval_comment']??'')?></td>
          <td><a class="btn btn-primary" href="/trials/<?=$t['id']?>/report">View Report</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="8" class="empty-state">Belum ada rejected report.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
