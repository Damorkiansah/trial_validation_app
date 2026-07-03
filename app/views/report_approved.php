<div class="page-heading">
  <div>
    <h1>Approved Report</h1>
    <p>Trial dengan status Approved.</p>
  </div>
  <button class="btn btn-light" onclick="window.print()">Print</button>
</div>

<section class="card table-card print-card">
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Trial ID</th><th>Product Name</th><th>Finish Good Code</th><th>Product Type</th><th>Approved Date</th><th>Approved By</th><th>Action</th></tr>
      <?php foreach($items as $t): ?>
        <tr>
          <td><?=h($t['trial_code'])?></td>
          <td><?=h($t['product_name'])?></td>
          <td><?=h($t['finish_good_code'])?></td>
          <td><?=h($t['product_type'])?></td>
          <td><?=h($t['approved_at']??'')?></td>
          <td><?=h(display_person_name($t['approved_by']??''))?></td>
          <td>
            <a class="btn btn-primary" href="/trials/<?=$t['id']?>/report">View Report</a>
            <button class="btn btn-light" onclick="window.print()">Print</button>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="7" class="empty-state">Belum ada approved report.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
