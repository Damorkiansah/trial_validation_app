<div class="page-heading">
  <div>
    <h1>Review Department</h1>
    <p>Trial yang perlu direview oleh department Anda.</p>
  </div>
</div>

<section class="card table-card">
  <div class="table-wrap">
    <table class="modern-table">
      <tr>
        <th>Trial</th>
        <th>Product</th>
        <th>Round</th>
        <th>Status</th>
        <th>Reviewer Name</th>
        <th>Comment</th>
      </tr>
      <?php foreach($items as $it):
        $active=$it['progress_status']==='In Review'&&(int)$it['review_round']===((int)$it['revision_no']+1)&&$it['status']==='Pending';
      ?>
        <tr>
          <td><a href="/trials/<?=$it['trial_id']?>/report"><?=h($it['trial_code'])?></a></td>
          <td><?=h($it['product_name'])?></td>
          <td><?=h($it['review_round'])?></td>
          <td><?=h($it['status'])?></td>
          <td><?=h($it['reviewer_name']?display_person_name($it['reviewer_name']):($active?user_display_name():''))?></td>
          <td>
            <?php if($active): ?>
              <form method="post" action="/review/<?=$it['id']?>/save">
                <?php csrf_field(); ?>
                <div class="readonly-meta"><span>Reviewer</span><strong><?=h(user_display_name())?></strong></div>
                <textarea name="comment" required></textarea>
                <button>Submit Review</button>
              </form>
            <?php else: ?>
              <?=h($it['comment'])?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="6" class="empty-state">Tidak ada review pending.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
