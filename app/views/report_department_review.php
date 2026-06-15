<div class="page-heading">
  <div>
    <h1>Department Review Report</h1>
    <p>Progress review per department.</p>
  </div>
  <button class="btn btn-light" onclick="window.print()">Print</button>
</div>

<section class="card table-card print-card">
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Trial ID</th><th>Product Name</th><th>PRD</th><th>RNI</th><th>QAC</th><th>PRNI</th><th>PI</th><th>Review Status</th><th>Pending Department</th><th>Action</th></tr>
      <?php foreach($items as $t):
        $statuses=[$t['PRD']??null,$t['RNI']??null,$t['QAC']??null,$t['PRNI']??null,$t['PI']??null];
        $requiredStatuses=array_values(array_filter($statuses,fn($status)=>$status&&$status!=='N/A'));
        $reviewStatus=in_array('Pending',$requiredStatuses,true)?'Pending':($requiredStatuses?'Reviewed':'N/A');
      ?>
        <tr>
          <td><?=h($t['trial_code'])?></td>
          <td><?=h($t['product_name'])?></td>
          <td><?=h($t['PRD']??'N/A')?></td>
          <td><?=h($t['RNI']??'N/A')?></td>
          <td><?=h($t['QAC']??'N/A')?></td>
          <td><?=h($t['PRNI']??'N/A')?></td>
          <td><?=h($t['PI']??'N/A')?></td>
          <td><?=h($reviewStatus)?></td>
          <td><?=h($t['pending_with'])?></td>
          <td><a class="btn btn-primary" href="/trials/<?=$t['id']?>/report">View Review</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="10" class="empty-state">Belum ada data review department.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
