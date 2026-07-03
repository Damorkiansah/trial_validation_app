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
      <tr>
        <th>Trial ID</th>
        <th>Product Name</th>
        <?php foreach(($reviewDepartments??reviewer_department_codes()) as $dept): ?><th><?=h($dept)?></th><?php endforeach; ?>
        <th>Review Status</th>
        <th>Pending Department</th>
        <th>Action</th>
      </tr>
      <?php foreach($items as $t):
        $statuses=[];
        foreach(($reviewDepartments??reviewer_department_codes()) as $dept){
          $key='dept_'.preg_replace('/[^A-Z0-9_]/','_',normalize_department($dept));
          $statuses[]=$t[$key]??null;
        }
        $requiredStatuses=array_values(array_filter($statuses,fn($status)=>$status&&$status!=='N/A'));
        $reviewStatus=in_array('Pending',$requiredStatuses,true)?'Pending':($requiredStatuses?'Reviewed':'N/A');
      ?>
        <tr>
          <td><?=h($t['trial_code'])?></td>
          <td><?=h($t['product_name'])?></td>
          <?php foreach(($reviewDepartments??reviewer_department_codes()) as $dept):
            $key='dept_'.preg_replace('/[^A-Z0-9_]/','_',normalize_department($dept));
          ?>
            <td><?=h($t[$key]??'N/A')?></td>
          <?php endforeach; ?>
          <td><?=h($reviewStatus)?></td>
          <td><?=h($t['pending_with'])?></td>
          <td><a class="btn btn-primary" href="/trials/<?=$t['id']?>/report">View Review</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="<?=h(count($reviewDepartments??reviewer_department_codes())+5)?>" class="empty-state">Belum ada data review department.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
