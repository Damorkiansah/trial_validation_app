<div class="page-heading">
  <div>
    <h1>Product Template</h1>
    <p>Kelola product dan Finish Good Code untuk input trial.</p>
  </div>
</div>

<form method="post" action="/templates/products/save" class="card grid">
  <?php csrf_field(); ?>
  <?php if(!empty($editProduct)): ?><input type="hidden" name="id" value="<?=h($editProduct['id'])?>"><?php endif; ?>
  <label>Product Name
    <input name="product_name" placeholder="Product Name" required value="<?=h($editProduct['product_name']??'')?>">
  </label>
  <label>Finish Good Code
    <input name="finish_good_code" placeholder="Finish Good Code" required value="<?=h($editProduct['finish_good_code']??'')?>">
  </label>
  <div class="filter-actions">
    <button><?=empty($editProduct)?'Add Product':'Update Product'?></button>
    <?php if(!empty($editProduct)): ?><a class="btn btn-light" href="/templates/products">Cancel</a><?php endif; ?>
  </div>
</form>

<section class="card table-card">
  <div class="table-header">
    <h2>Products</h2>
    <span><?=count($products)?> active item(s)</span>
  </div>
  <div class="table-wrap">
    <table class="modern-table">
      <tr><th>Product</th><th>FG Code</th><th>Action</th></tr>
      <?php foreach($products as $p): ?>
        <tr>
          <td><?=h($p['product_name'])?></td>
          <td><?=h($p['finish_good_code'])?></td>
          <td class="actions">
            <a class="btn btn-light" href="/templates/products?edit=<?=$p['id']?>">Edit</a>
            <form method="post" action="/templates/products/<?=$p['id']?>/delete" class="inline-form" onsubmit="return confirm('Delete product template ini? Data lama trial tidak ikut terhapus.')">
              <?php csrf_field(); ?>
              <button class="danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(!$products): ?><tr><td colspan="3" class="empty-state">Belum ada product aktif.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php render_pagination($pagination??null); ?>
</section>
