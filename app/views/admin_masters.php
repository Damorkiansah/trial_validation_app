<?php
$masterTypes=['validation_category','validation_scope','machine_used','product_type','attachment_category'];
$currentType=$editMaster['type']??'validation_category';
?>
<div class="page-heading">
  <div>
    <h1>Master Template</h1>
    <p>Kelola pilihan dropdown untuk trial validation.</p>
  </div>
</div>

<form method="post" action="/templates/master/save" class="card grid">
  <?php csrf_field(); ?>
  <?php if(!empty($editMaster)): ?><input type="hidden" name="id" value="<?=h($editMaster['id'])?>"><?php endif; ?>
  <label>Type
    <select name="type">
      <?php foreach($masterTypes as $type): ?>
        <option value="<?=h($type)?>" <?=$currentType===$type?'selected':''?>><?=h($type)?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Name
    <input name="name" placeholder="Name" required value="<?=h($editMaster['name']??'')?>">
  </label>
  <label>Sort
    <input name="sort_order" type="number" placeholder="Sort" value="<?=h($editMaster['sort_order']??0)?>">
  </label>
  <div class="filter-actions">
    <button><?=empty($editMaster)?'Add Master':'Update Master'?></button>
    <?php if(!empty($editMaster)): ?><a class="btn btn-light" href="/templates/master">Cancel</a><?php endif; ?>
  </div>
</form>

<section class="card table-card">
  <div class="table-header">
    <h2>Master Options</h2>
    <span><?=count($opts)?> active item(s)</span>
  </div>
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Type</th><th>Name</th><th>Sort</th><th>Action</th></tr>
      <?php foreach($opts as $o): ?>
        <tr>
          <td><?=h($o['type'])?></td>
          <td><?=h($o['name'])?></td>
          <td><?=h($o['sort_order'])?></td>
          <td class="actions">
            <a class="btn btn-light" href="/templates/master?edit=<?=$o['id']?>">Edit</a>
            <form method="post" action="/templates/master/<?=$o['id']?>/delete" class="inline-form" onsubmit="return confirm('Delete master option ini? Item hanya dinonaktifkan.')">
              <?php csrf_field(); ?>
              <button class="danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$opts): ?><tr><td colspan="4" class="empty-state">Belum ada master option aktif.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
