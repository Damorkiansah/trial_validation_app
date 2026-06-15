<div class="page-heading">
  <div>
    <h1>Attachments</h1>
    <p>Upload dan kelola evidence foto trial.</p>
  </div>
</div>

<?php if(can_edit($t)): ?>
  <form method="post" action="/trials/<?=$t['id']?>/attachments/upload" enctype="multipart/form-data" class="card attachment-upload-card">
    <?php csrf_field(); ?>
    <div class="grid">
      <label>Category
        <select name="category">
          <?php foreach($cats as $c): ?><option><?=h($c['name'])?></option><?php endforeach; ?>
        </select>
      </label>
      <label>Photos
        <input id="attachment-input" type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp,image/gif">
      </label>
    </div>
    <div id="upload-preview" class="attachment-grid upload-preview-grid"></div>
    <button>Upload</button>
  </form>
<?php else: ?>
  <div class="flash">Attachment readonly. Foto hanya bisa dihapus saat status Draft atau Need Revision.</div>
<?php endif; ?>

<?php
$g=[];
foreach($files as $f) $g[$f['category']][]=$f;
foreach($g as $cat=>$fs):
?>
  <section class="card attachment-section">
    <div class="table-header">
      <h2><?=h($cat)?></h2>
      <span><?=count($fs)?> foto</span>
    </div>
    <div class="attachment-grid">
      <?php foreach($fs as $f): ?>
        <figure class="attachment-tile">
          <img class="photo-preview" src="<?=h($f['file_path'])?>" data-caption="<?=h($cat)?> - <?=h($f['file_name'])?>">
          <figcaption><?=h($f['file_name'])?></figcaption>
          <?php if(can_edit($t)): ?>
            <form method="post" action="/trials/<?=$t['id']?>/attachments/<?=$f['id']?>/delete" onsubmit="return confirm('Remove this photo?')">
              <?php csrf_field(); ?>
              <button class="delete-photo" type="submit" title="Remove photo">Delete</button>
            </form>
          <?php endif; ?>
        </figure>
      <?php endforeach; ?>
    </div>
  </section>
<?php endforeach; ?>

<?php if(!$files): ?>
  <section class="card empty-state">Belum ada attachment.</section>
<?php endif; ?>

<p>
  <a class="btn btn-light" href="/trials/<?=$t['id']?>/weighing/Filling">Back</a>
  <a class="btn btn-primary" href="/trials/<?=$t['id']?>/report">Open Summary</a>
</p>

<script>
(function(){
  const input=document.getElementById('attachment-input');
  const preview=document.getElementById('upload-preview');
  if(!input||!preview) return;
  let selected=[];
  function syncFiles(){
    const dt=new DataTransfer();
    selected.forEach(file=>dt.items.add(file));
    input.files=dt.files;
  }
  function render(){
    preview.innerHTML='';
    selected.forEach(function(file,index){
      const tile=document.createElement('figure');
      tile.className='attachment-tile pending-tile';
      const img=document.createElement('img');
      img.src=URL.createObjectURL(file);
      img.onload=function(){URL.revokeObjectURL(img.src)};
      const cap=document.createElement('figcaption');
      cap.textContent=file.name;
      const btn=document.createElement('button');
      btn.type='button';
      btn.className='delete-photo';
      btn.textContent='Remove';
      btn.onclick=function(){selected.splice(index,1);syncFiles();render();};
      tile.appendChild(img);
      tile.appendChild(cap);
      tile.appendChild(btn);
      preview.appendChild(tile);
    });
  }
  input.addEventListener('change',function(){
    selected=Array.from(input.files);
    render();
  });
})();
</script>
