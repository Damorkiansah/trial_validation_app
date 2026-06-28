<div class="page-heading no-print">
  <div>
    <h1>Report Summary</h1>
    <p>Trial validation summary dan attachment evidence.</p>
  </div>
  <button class="btn btn-primary" onclick="printTrialReport()">Print</button>
</div>

<?php if(can_edit($t)): ?>
  <div class="no-print">
    <a class="btn btn-light" href="/trials/<?=$t['id']?>/attachments">Edit / Back</a>
    <?php if($completeness): ?>
      <div class="flash">
        Belum siap submit review:
        <ul>
          <?php foreach($completeness as $item): ?><li><?=h($item)?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php else: ?>
      <button type="button" class="danger" data-review-modal-open>Submit for Review</button>
      <div class="review-modal" id="review-department-modal" aria-hidden="true">
        <div class="review-modal-panel">
          <div class="review-modal-head">
            <h2>Select Review Department / Team</h2>
            <button type="button" class="btn btn-light" data-review-modal-close>Cancel</button>
          </div>
          <form method="post" action="/trials/<?=$t['id']?>/submit-review" id="submit-review-form">
            <?php csrf_field(); ?>
            <div class="review-dept-grid">
              <?php foreach(reviewer_department_codes() as $dept): ?>
                <label class="review-dept-choice">
                  <input type="checkbox" name="departments[]" value="<?=h($dept)?>" checked>
                  <span><?=h($dept)?></span>
                </label>
              <?php endforeach; ?>
            </div>
            <p class="review-modal-error" id="review-modal-error">Please select at least one review department.</p>
            <div class="filter-actions">
              <button type="button" class="btn btn-light" data-review-modal-close>Cancel</button>
              <button class="danger">Submit for Review</button>
            </div>
          </form>
        </div>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>

<section class="print-card report-sheet">
  <header class="print-report-header">
    <div class="print-logo-wrap">
      <img src="/assets/cosmax-idn-logo.jpg" alt="COSMAX Indonesia">
    </div>
    <div class="print-report-title">Trial Validation System Report</div>
    <div class="print-form-number">FR.QSE.074.04</div>
  </header>

  <section class="report-info-grid">
    <div><span>Trial ID</span><strong><?=h($t['trial_code'])?></strong></div>
    <div><span>Product Name</span><strong><?=h($t['product_name'])?></strong></div>
    <div><span>FG Code</span><strong><?=h($t['finish_good_code'])?></strong></div>
    <div><span>Validation Category</span><strong><?=h($t['validation_category'])?></strong></div>
    <div><span>Validation Scope</span><strong><?=h(display_multi_value($t['validation_scope']))?></strong></div>
    <div><span>Product Type</span><strong><?=h($t['product_type'])?></strong></div>
    <div><span>Validation Date</span><strong><?=h($t['validation_date'])?></strong></div>
    <div><span>Risk Level</span><strong><?=h($t['risk_level'])?></strong></div>
    <div><span>Machine Used</span><strong><?=h(display_multi_value($t['machine_used']))?></strong></div>
    <div><span>Created By</span><strong><?=h($t['created_by'])?></strong></div>
    <div><span>Final Approval Status</span><strong><?=h($t['final_decision']?:$t['progress_status'])?></strong></div>
    <div><span>Manager QAC Name</span><strong><?=h(display_person_name($t['approved_by']?:($t['rejected_by']?:''))?:'-')?></strong></div>
  </section>

  <h3>Header Detail</h3>
  <table class="report-table">
    <tr><th>Batch Number</th><td><?=h($t['batch_number']??'')?></td><th>Bulk Code</th><td><?=h($t['bulk_code']??'')?></td></tr>
    <tr><th>Support Team</th><td><?=h($t['support_team']??'')?></td><th>Initiated person/team</th><td><?=h($t['initiated_person_team']??'')?></td></tr>
    <tr><th>Reason</th><td colspan="3"><?=h($t['reason']??'')?></td></tr>
    <tr><th>B.O.M</th><td colspan="3"><?=nl2br(h($t['bom']??''))?></td></tr>
    <tr><th>Status</th><td><?=h($t['progress_status'])?></td><th>Pending With</th><td><?=h($t['pending_with'])?></td></tr>
    <tr><th>Revision No</th><td><?=h($t['revision_no']??0)?></td><th>Final Decision</th><td><?=h($t['final_decision'])?></td></tr>
  </table>

  <h3>Validation Parameter</h3>
  <table class="report-table">
    <tr><th>Parameter</th><th>Spec</th><th>Decision</th><th>Result</th><th>Remark</th></tr>
    <?php foreach($results as $r): ?>
      <tr class="<?=$r['decision']==='NOT OK'?'notok':''?>">
        <td><?=h($r['parameter_name'])?></td>
        <td><?=nl2br(h($r['specification']))?></td>
        <td><?=h($r['decision'])?></td>
        <td><?=h($r['result_value'])?></td>
        <td><?=h($r['remark'])?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h3>Weighing</h3>
  <?php foreach(['Packaging','Filling'] as $sec):
    $items=array_values(array_filter($weigh,fn($x)=>$x['section']===$sec));
    $stats=weighing_stats($items);
    $sk=$items&&count(array_filter($items,fn($x)=>(int)$x['is_skipped']===1))>0&&$stats['count']===0;
  ?>
    <h4><?=h($sec)?> Weighing</h4>
    <?php if($sk): ?>
      <p><b><?=h($sec)?> Weighing: N/A</b></p>
    <?php else: ?>
      <div class="weigh-report">
        <?php foreach($items as $it): ?>
          <?php if((int)$it['is_skipped']!==1&&$it['weight_value']!==null&&$it['weight_value']!==''&&is_numeric($it['weight_value'])): ?>
            <span><?=h(format_weight_value($it['weight_value'],3))?></span>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <table class="report-table weigh-summary">
        <tr><th>Total Sample</th><td><?=h($stats['count'])?></td><th>Average</th><td><?=h($stats['count']?number_format($stats['avg'],2):'-')?></td></tr>
        <tr><th>Minimum</th><td><?=h($stats['count']?number_format($stats['min'],2):'-')?></td><th>Maximum</th><td><?=h($stats['count']?number_format($stats['max'],2):'-')?></td></tr>
      </table>
    <?php endif; ?>
  <?php endforeach; ?>

  <section class="attachment-summary">
    <h3>Attachment Summary</h3>
    <?php
    $g=[];
    foreach($files as $f) $g[$f['category']][]=$f;
    foreach($g as $cat=>$fs):
      $chunks=array_chunk($fs,6);
      foreach($chunks as $chunkIndex=>$chunk):
    ?>
      <section class="attachment-category print-attachment-category <?=$chunkIndex>0?'print-page-break':''?>">
        <h4><?=h($cat)?><?=count($fs)>6?' - Page '.($chunkIndex+1):''?></h4>
        <div class="attachment-grid-print print-attachment-grid">
          <?php foreach($chunk as $f): ?>
            <figure class="attachment-photo print-attachment-tile">
              <img class="image-print photo-preview" src="<?=h($f['file_path'])?>" data-caption="<?=h($cat)?> - <?=h($f['file_name'])?>">
              <figcaption><?=h($f['file_name'])?></figcaption>
            </figure>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; endforeach; ?>
    <?php if(!$files): ?><p class="empty-state">Tidak ada attachment.</p><?php endif; ?>
  </section>

  <h3>Department Review</h3>
  <table class="report-table">
    <tr><th>Round</th><th>Dept</th><th>Status</th><th>Reviewer Name</th><th>Reviewed At</th><th>Comment</th></tr>
    <?php
    $reviewByDept=[];
    $activeRound=current_review_round($t);
    $availableRounds=[];
    foreach($reviews as $rv){
      $availableRounds[]=(int)($rv['review_round']??1);
    }
    $availableRounds=array_values(array_unique($availableRounds));
    if($availableRounds&&!in_array($activeRound,$availableRounds,true)){
      $activeRound=max($availableRounds);
    }
    foreach($reviews as $rv){
      if((int)($rv['review_round']??1)===$activeRound) $reviewByDept[$rv['department']]=$rv;
    }
    foreach(reviewer_department_codes() as $dept):
      $rv=$reviewByDept[$dept]??null;
    ?>
      <tr>
        <td><?=h($rv['review_round']??$activeRound)?></td>
        <td><?=h($dept)?></td>
        <td><?=h($rv['status']??'N/A')?></td>
        <td><?=h(display_person_name($rv['reviewer_name']??''))?></td>
        <td><?=h($rv['reviewed_at']??'')?></td>
        <td><?=h($rv['comment']??'')?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <?php if(!empty($t['approved_by'])||!empty($t['rejected_by'])||!empty($t['approval_comment'])): ?>
    <h3>Manager QAC Decision</h3>
    <table class="report-table">
      <tr><th>Approved By </th><td><?=h(display_person_name($t['approved_by']??''))?></td><th>Approved At</th><td><?=h($t['approved_at']??'')?></td></tr>
      <tr><th>Rejected By  </th><td><?=h(display_person_name($t['rejected_by']??''))?></td><th>Rejected At</th><td><?=h($t['rejected_at']??'')?></td></tr>
      <tr><th>Comment</th><td colspan="3"><?=h($t['approval_comment']??'')?></td></tr>
    </table>
  <?php endif; ?>
</section>

<script>
function printTrialReport(){
  const data=new FormData();
  data.append('csrf_token','<?=h(csrf_token())?>');
  fetch('/trials/<?=$t['id']?>/print-log',{method:'POST',body:data}).catch(()=>{});
  window.print();
}
(function(){
  const openBtn=document.querySelector('[data-review-modal-open]');
  const modal=document.getElementById('review-department-modal');
  if(!openBtn||!modal) return;
  const form=document.getElementById('submit-review-form');
  const error=document.getElementById('review-modal-error');
  function open(){modal.classList.add('show');modal.setAttribute('aria-hidden','false');}
  function close(){modal.classList.remove('show');modal.setAttribute('aria-hidden','true');if(error) error.classList.remove('show');}
  openBtn.addEventListener('click',open);
  modal.querySelectorAll('[data-review-modal-close]').forEach(btn=>btn.addEventListener('click',close));
  modal.addEventListener('click',function(e){if(e.target===modal) close();});
  form.addEventListener('submit',function(e){
    if(!form.querySelector('input[name="departments[]"]:checked')){
      e.preventDefault();
      if(error) error.classList.add('show');
    }
  });
})();
</script>
