<div class="page-heading">
  <div>
    <h1>Final Approval</h1>
    <p>Trial yang sudah selesai review dan menunggu final approval.</p>
  </div>
</div>

<section class="card table-card">
  <div class="table-wrap">
    <table class="modern-table">
      <tr>
        <th>Trial</th>
        <th>Product</th>
        <th>Risk</th>
        <th>Assigned Approver</th>
        <th>Aksi</th>
      </tr>
      <?php foreach($items as $t):
        $assignedApproverDisplay = !empty($t['approver_name']) ? h($t['approver_name']) : (!empty($t['approver_email']) ? h($t['approver_email']) : '-');
        $approverLabel = u()['department'] ?? role();
      ?>
        <tr>
          <td><a href="/trials/<?=$t['id']?>/report"><?=h($t['trial_code'])?></a></td>
          <td><?=h($t['product_name'])?></td>
          <td><?=h($t['risk_level'])?></td>
          <td><?=h($assignedApproverDisplay)?></td>
          <td>
            <form method="post" action="/trials/<?=$t['id']?>/approval" class="approval-form">
              <?php csrf_field(); ?>
              <div class="readonly-meta"><span><?=h($approverLabel)?></span><strong><?=h(user_display_name())?></strong></div>
              <textarea name="approval_comment" placeholder="Decision comment" required></textarea>
              <input type="password" name="signature_password" placeholder="Password e-signature" required>
              <button name="decision" value="Approved" data-confirm="Approve trial ini?">Approve</button>
              <button class="btn btn-light" name="decision" value="Need Revision" data-confirm="Kembalikan trial ini ke Staff sebagai Need Revision?">Need Revision</button>
              <button class="danger" name="decision" value="Rejected" data-confirm="Reject final trial ini? Trial tidak bisa direvisi lagi.">Reject</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="5" class="empty-state">Tidak ada trial menunggu approval.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>

<script>
document.querySelectorAll('.approval-form button[name="decision"]').forEach(function(button){
  button.addEventListener('click', function(e){
    if(!confirm(button.getAttribute('data-confirm') || 'Lanjutkan aksi ini?')){
      e.preventDefault();
    }
  });
});
</script>
