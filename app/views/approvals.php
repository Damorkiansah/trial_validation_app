<div class="page-heading">
  <div>
    <h1>Manager QAC Approval</h1>
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
        <th>Aksi</th>
      </tr>
      <?php foreach($items as $t): ?>
        <tr>
          <td><a href="/trials/<?=$t['id']?>/report"><?=h($t['trial_code'])?></a></td>
          <td><?=h($t['product_name'])?></td>
          <td><?=h($t['risk_level'])?></td>
          <td>
            <form method="post" action="/trials/<?=$t['id']?>/approval" class="approval-form">
              <?php csrf_field(); ?>
              <div class="readonly-meta"><span>Manager QAC</span><strong><?=h(user_display_name())?></strong></div>
              <textarea name="approval_comment" placeholder="Decision comment" required></textarea>
              <input type="password" name="signature_password" placeholder="Password e-signature" required>
              <button name="decision" value="Approved">Approve</button>
              <button class="danger" name="decision" value="Rejected">Reject / Need Revision</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="4" class="empty-state">Tidak ada trial menunggu approval.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
