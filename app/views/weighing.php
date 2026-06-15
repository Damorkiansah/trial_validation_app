<h2>Weighing <?=h(section_display_name($section))?></h2>
<?php if(can_edit($t)): ?>
  <form method="post" action="/trials/<?=$t['id']?>/weighing/<?=$section?>/save">
    <?php csrf_field(); ?>
<?php endif; ?>
<label>
  <input type="checkbox" name="skip" id="skip" <?=$skip?'checked':''?> <?=can_edit($t)?'':'disabled'?>> Skip <?=h(section_display_name($section))?> (N/A)
</label>
<?php $maxItem=$vals?max(30,max(array_map('intval',array_keys($vals)))):30; ?>
<div class="weigh-grid" id="weigh-grid">
  <?php for($i=1;$i<=$maxItem;$i++): ?>
    <label><?=$i?>
      <input type="number" step="0.001" min="0" inputmode="decimal" name="w[<?=$i?>]" class="w" value="<?=h($vals[$i]??'')?>" <?=can_edit($t)?'':'readonly'?>>
    </label>
  <?php endfor; ?>
</div>
<?php if(can_edit($t)): ?><button type="button" class="btn btn-light" id="add-sample">Add Sample</button><?php endif; ?>
<div class="stats">
  <div><b>Total Sample :</b> <span id="total">0</span></div>
  <div><b>Average :</b> <span id="avg">-</span></div>
  <div><b>Minimum :</b> <span id="min">-</span></div>
  <div><b>Maximum :</b> <span id="max">-</span></div>
</div>
<?php if(can_edit($t)): ?><p class="weigh-error" id="weigh-error">Please input at least 1 weighing sample or click Skip.</p><?php endif; ?>
<p>
  <a class="btn" href="<?=$section==='Packaging'?'/trials/'.$t['id'].'/validation':'/trials/'.$t['id'].'/weighing/Packaging'?>">Back</a>
  <?php if(can_edit($t)): ?><button>Save &amp; Next</button><?php endif; ?>
</p>
<?php if(can_edit($t)): ?></form><?php endif; ?>
<script>
function calc(){
  let a=[];
  $('.w').each(function(){let v=parseFloat(this.value);if(!isNaN(v)) a.push(v)});
  $('#total').text(a.length);
  $('#avg').text(a.length?(a.reduce((x,y)=>x+y,0)/a.length).toFixed(2):'-');
  $('#min').text(a.length?Math.min(...a).toFixed(2):'-');
  $('#max').text(a.length?Math.max(...a).toFixed(2):'-');
}
$(document).on('input','.w',calc);
function nextSampleNo(){
  let nums=$('.w').toArray().map(input=>{
    let match=input.name.match(/\[(\d+)\]/);
    return match?parseInt(match[1],10):0;
  });
  return Math.max(0,...nums)+1;
}
$('#add-sample').on('click',function(){
  let n=nextSampleNo();
  $('#weigh-grid').append('<label>'+n+'<input type="number" step="0.001" min="0" inputmode="decimal" name="w['+n+']" class="w"></label>');
});
$('#skip').on('change',function(){
  $('.w').prop('disabled',this.checked);
  $('#add-sample').prop('disabled',this.checked);
}).trigger('change');
<?php if(can_edit($t)): ?>
$('form').on('submit',function(){
  let hasValue=$('.w').toArray().some(input=>String(input.value).trim()!=='');
  let ok=$('#skip').is(':checked')||hasValue;
  $('#weigh-error').toggleClass('show',!ok);
  return ok;
});
<?php endif; ?>
calc();
</script>
