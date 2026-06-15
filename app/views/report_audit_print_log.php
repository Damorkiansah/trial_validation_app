<div class="page-heading">
  <div>
    <h1>Audit Print Log</h1>
    <p>Log aktivitas print report jika tersedia.</p>
  </div>
</div>

<section class="card table-card">
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Trial ID</th><th>Printed By</th><th>Printed At</th><th>Report Type</th></tr>
      <?php foreach($items as $it):
        $data=json_decode($it['new_data']??'',true)?:[];
      ?>
        <tr>
          <td><?=h($it['trial_id'])?></td>
          <td><?=h($it['printed_by'])?></td>
          <td><?=h($it['printed_at'])?></td>
          <td><?=h($data['report_type']??'Report')?></td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$items): ?><tr><td colspan="4" class="empty-state">Belum ada audit print log.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
