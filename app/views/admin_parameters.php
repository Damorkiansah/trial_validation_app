<div class="page-heading">
  <div>
    <h1>Parameter Template</h1>
    <p>Kelola parameter validasi berdasarkan product type.</p>
  </div>
</div>

<form method="post" action="/templates/parameters/save" class="card grid">
  <?php csrf_field(); ?>
  <?php if(!empty($editParameter)): ?><input type="hidden" name="id" value="<?=h($editParameter['id'])?>"><?php endif; ?>
  <label>Product Type
    <select name="product_type">
      <?php foreach(opts('product_type') as $o): ?>
        <option value="<?=h($o['name'])?>" <?=($editParameter['product_type']??'')===$o['name']?'selected':''?>><?=h($o['name'])?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Parameter
    <input name="parameter_name" placeholder="Parameter" required value="<?=h($editParameter['parameter_name']??'')?>">
  </label>
  <label>Sort
    <input name="sort_order" type="number" placeholder="Sort" value="<?=h($editParameter['sort_order']??0)?>">
  </label>
  <label>Specification
    <textarea name="specification" placeholder="Specification"><?=h($editParameter['specification']??'')?></textarea>
  </label>
  <div class="filter-actions">
    <button><?=empty($editParameter)?'Add Parameter':'Update Parameter'?></button>
    <?php if(!empty($editParameter)): ?><a class="btn btn-light" href="/templates/parameters">Cancel</a><?php endif; ?>
  </div>
</form>

<section class="card table-card">
  <div class="table-header">
    <h2>Validation Parameters</h2>
    <span><?=count($params)?> active item(s)</span>
  </div>
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Product Type</th><th>Parameter</th><th>Specification</th><th>Sort</th><th>Action</th></tr>
      <?php foreach($params as $p): ?>
        <tr>
          <td><?=h($p['product_type'])?></td>
          <td><?=h($p['parameter_name'])?></td>
          <td><?=nl2br(h($p['specification']))?></td>
          <td><?=h($p['sort_order'])?></td>
          <td class="actions">
            <a class="btn btn-light" href="/templates/parameters?edit=<?=$p['id']?>">Edit</a>
            <form method="post" action="/templates/parameters/<?=$p['id']?>/delete" class="inline-form" onsubmit="return confirm('Delete parameter ini? Data lama hasil trial tidak ikut terhapus.')">
              <?php csrf_field(); ?>
              <button class="danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$params): ?><tr><td colspan="5" class="empty-state">Belum ada parameter aktif.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
