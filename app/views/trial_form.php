<?php
$old=$_SESSION['old']??[];
unset($_SESSION['old']);
$v=function($k)use($old,$trial){
 $value=$old[$k]??($trial[$k]??'');
 return h(is_array($value)?'':$value);
};
$selected=function($k)use($old,$trial){return multi_values($old[$k]??($trial[$k]??''));};
?>
<h2><?= $trial?'Edit':'New' ?> Trial</h2>
<form method="post" action="<?=$trial?'/trials/'.$trial['id'].'/update':'/trials/store'?>" class="card" novalidate>
  <?php csrf_field(); ?>
  <div class="grid">
    <label>Product Name *
      <select name="product_id" id="product" class="select2 required" required>
        <option value="">Pilih/Search Product</option>
        <?php foreach($products as $p): ?>
          <option value="<?=$p['id']?>" data-fg="<?=h($p['finish_good_code'])?>" <?=($v('product_id')==$p['id']||($trial&&$trial['product_id']==$p['id']))?'selected':''?>><?=h($p['product_name'])?></option>
        <?php endforeach; ?>
      </select>
      <small class="err">Wajib diisi</small>
    </label>
    <label>Finish Good Code
      <input id="fg" readonly value="<?=h($trial['finish_good_code']??'')?>">
    </label>
    <label>Product Type *
      <select name="product_type" required>
        <option value="">Pilih Product Type</option>
        <?php foreach(opts('product_type') as $o): ?>
          <option <?=$v('product_type')==$o['name']?'selected':''?>><?=h($o['name'])?></option>
        <?php endforeach; ?>
      </select>
      <small class="err">Wajib diisi</small>
    </label>
    <label>Validation Date *
      <input type="date" name="validation_date" value="<?=$v('validation_date')?>" required>
      <small class="err">Wajib diisi</small>
    </label>
    <label>Risk Level *
      <select name="risk_level" required>
        <option value="">Pilih Risk</option>
        <?php foreach(['Low','Medium','High'] as $x): ?>
          <option <?=$v('risk_level')==$x?'selected':''?>><?=$x?></option>
        <?php endforeach; ?>
      </select>
      <small class="err">Wajib diisi</small>
    </label>
    <label>Validation Category *
      <select name="validation_category" required>
        <option value="">Pilih Category</option>
        <?php foreach(opts('validation_category') as $o): ?>
          <option <?=$v('validation_category')==$o['name']?'selected':''?>><?=h($o['name'])?></option>
        <?php endforeach; ?>
      </select>
      <small class="err">Wajib diisi</small>
    </label>
    <label>Validation Scope *
      <?php $selectedScope=$selected('validation_scope'); ?>
      <select name="validation_scope[]" class="select2 multi-required" multiple required data-placeholder="Pilih/Search Scope" data-error="Please select at least one validation scope.">
        <?php foreach(opts('validation_scope') as $o): ?>
          <option value="<?=h($o['name'])?>" <?=in_array($o['name'],$selectedScope,true)?'selected':''?>><?=h($o['name'])?></option>
        <?php endforeach; ?>
      </select>
      <small class="err">Please select at least one validation scope.</small>
    </label>
    <label>Machine Used *
      <?php $selectedMachine=$selected('machine_used'); ?>
      <select name="machine_used[]" class="select2 multi-required" multiple required data-placeholder="Pilih/Search Machine" data-error="Please select at least one machine.">
        <?php foreach(opts('machine_used') as $o): ?>
          <option value="<?=h($o['name'])?>" <?=in_array($o['name'],$selectedMachine,true)?'selected':''?>><?=h($o['name'])?></option>
        <?php endforeach; ?>
      </select>
      <small class="err">Please select at least one machine.</small>
    </label>
    <label>Estimate Qty *
      <input name="estimate_qty" type="number" step="1" min="0" value="<?=$v('estimate_qty')?>" required>
      <small class="err">Wajib angka</small>
    </label>
    <label>Batch Number *
      <input name="batch_number" type="text" value="<?=$v('batch_number')?>" required>
      <small class="err">Wajib diisi</small>
    </label>
    <label>Bulk Code *
      <input name="bulk_code" type="text" value="<?=$v('bulk_code')?>" required>
      <small class="err">Wajib diisi</small>
    </label>
    <label>Support Team *
      <input name="support_team" type="text" value="<?=$v('support_team')?>" required>
      <small class="err">Wajib diisi</small>
    </label>
    <label>Initiated person/team *
      <input name="initiated_person_team" type="text" value="<?=$v('initiated_person_team')?>" required>
      <small class="err">Wajib diisi</small>
    </label>
    <label>Reason *
      <input name="reason" type="text" value="<?=$v('reason')?>" required>
      <small class="err">Wajib diisi</small>
    </label>
    <label>B.O.M *
      <textarea name="bom" required><?=$v('bom')?></textarea>
      <small class="err">Wajib diisi</small>
    </label>
  </div>
  <button>Save &amp; Next</button>
</form>
<script>
function fg(){let o=$('#product option:selected');$('#fg').val(o.data('fg')||'')}
$('#product').on('change',fg);fg();
$('form').on('submit',function(){
  let ok=true;
  $(this).find('[required]').each(function(){
    let val=$(this).val();
    let bad=Array.isArray(val)?val.length===0:!val;
    $(this).closest('label').toggleClass('invalid',bad);
    if(bad) ok=false;
  });
  return ok;
});
</script>
