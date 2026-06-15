<h2>Validation Trial Parameter - <?=h($t['product_type'])?></h2>
<?php if(!$params): ?>
  <div class="flash">Parameter validation untuk product type ini belum dikonfigurasi.</div>
<?php else: ?>
  <?php if(can_edit($t)): ?>
    <form method="post" action="/trials/<?=$t['id']?>/validation/save">
      <?php csrf_field(); ?>
  <?php endif; ?>
  <table>
    <tr>
      <th>Parameter</th>
      <th>Specification</th>
      <th>Decision</th>
      <th>Result</th>
      <th>Remark</th>
    </tr>
    <?php foreach($params as $i=>$p): $r=$rows[$p['id']]??[]; ?>
      <tr class="param-row <?=($r['decision']??'')==='NOT OK'?'notok':''?>">
        <td>
          <?=h($p['parameter_name'])?>
          <input type="hidden" name="parameter_id[]" value="<?=$p['id']?>">
        </td>
        <td><?=nl2br(h($p['specification']))?></td>
        <td>
          <select name="decision[]" class="dec" <?=can_edit($t)?'':'disabled'?>>
            <option <?=($r['decision']??'OK')==='OK'?'selected':''?>>OK</option>
            <option <?=($r['decision']??'')==='NOT OK'?'selected':''?>>NOT OK</option>
            <option <?=($r['decision']??'')==='N/A'?'selected':''?>>N/A</option>
          </select>
        </td>
        <td><input name="result[]" class="res" value="<?=h($r['result_value']??'Conform')?>" <?=can_edit($t)?'':'readonly'?>></td>
        <td><textarea name="remark[]" class="rem" <?=can_edit($t)?'':'readonly'?>><?=h($r['remark']??'')?></textarea></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <p>
    <a class="btn" href="<?=can_edit($t)?'/trials/'.$t['id'].'/edit':'/trials/'.$t['id'].'/report'?>">Back</a>
    <?php if(can_edit($t)): ?><button>Save &amp; Next</button><?php endif; ?>
  </p>
  <?php if(can_edit($t)): ?></form><?php endif; ?>
<?php endif; ?>
<script>
$('.dec').on('change',function(){
  let tr=$(this).closest('tr'),v=this.value,res=tr.find('.res');
  tr.toggleClass('notok',v==='NOT OK');
  if(v==='N/A') res.val('N/A');
  if(v==='OK') res.val(res.val().trim()===''?'Conform':res.val());
});
$('form').on('submit',function(){
  let ok=true;
  $('.param-row').each(function(){
    let dec=$(this).find('.dec').val(),res=$(this).find('.res').val().trim(),rem=$(this).find('.rem').val().trim();
    if(dec==='NOT OK'&&(res===''||rem==='')){
      ok=false;
      $(this).addClass('notok invalid-row');
    }
  });
  if(!ok) alert('NOT OK wajib isi Result dan Remark.');
  return ok;
});
</script>
