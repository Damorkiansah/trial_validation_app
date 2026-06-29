<div class="page-header">
  <h2>Trash - Deleted Trials</h2>
  <p class="subtitle">Restore atau hapus permanen trial yang sudah terhapus</p>
</div>

<div class="filter-bar">
  <form method="get" action="/admin/trash" class="filter-form">
    <input type="text" name="q" placeholder="Search trial code, product..." value="<?=h($filters['q'])?>" class="form-control">
    <input type="text" name="deleted_by" placeholder="Deleted by..." value="<?=h($filters['deleted_by'])?>" class="form-control">
    <input type="date" name="date_from" value="<?=h($filters['date_from'])?>" class="form-control">
    <input type="date" name="date_to" value="<?=h($filters['date_to'])?>" class="form-control">
    <button type="submit" class="btn btn-secondary">Filter</button>
    <a href="/admin/trash" class="btn btn-light">Reset</a>
  </form>
</div>

<?php if(empty($items)): ?>
  <div class="empty-state">
    <div class="empty-icon">🗑️</div>
    <p>Tidak ada trial yang terhapus.</p>
  </div>
<?php else: ?>
  <div class="info-bar">
    <span><?=count($items)?> dari <?=$pagination['total']?> item</span>
  </div>

  <table class="data-table">
    <thead>
      <tr>
        <th>Trial Code</th>
        <th>Product</th>
        <th>Product Type</th>
        <th>Created By</th>
        <th>Deleted By</th>
        <th>Deleted At</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($items as $item): ?>
        <tr>
          <td><strong><?=h($item['trial_code'])?></strong></td>
          <td><?=h($item['product_name'])?></td>
          <td><?=h($item['product_type'])?></td>
          <td><?=h($item['creator_name'] ?: $item['created_by'])?></td>
          <td><?=h($item['deleted_by_name'] ?: $item['deleted_by_email'] ?: '-')?></td>
          <td>
            <span class="text-muted"><?=date('d M Y', strtotime($item['deleted_at']))?></span>
            <br><small><?=date('H:i', strtotime($item['deleted_at']))?></small>
          </td>
          <td>
            <?php
            $status=$item['progress_status'];
            $final=$item['final_decision']??'';
            echo '<span class="badge '.h(status_class($status,$final)).'">'.h($status).'</span>';
            ?>
          </td>
          <td class="action-cell">
            <div class="action-buttons">
              <form method="post" action="/admin/trash/restore/<?=(int)$item['id']?>" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?=h(csrf_token())?>">
                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Restore trial ini?')">
                  ↩️ Restore
                </button>
              </form>
              <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(<?=(int)$item['id']?>, '<?=h(addslashes($item['trial_code']))?>')">
                🗑️ Delete
              </button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php render_pagination($pagination); ?>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display:none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Permanent Delete Confirmation</h3>
      <button type="button" class="modal-close" onclick="closeDeleteModal()">&times;</button>
    </div>
    <div class="modal-body">
      <p class="warning-text">
        ⚠️ <strong>Peringatan! Tindakan ini tidak dapat dibatalkan!</strong>
      </p>
      <p>Data trial akan dihapus secara <strong>PERMANEN</strong> dari database, termasuk:</p>
      <ul>
        <li>Data validasi</li>
        <li>Data weighing</li>
        <li>Review history</li>
        <li>Lampiran files</li>
        <li>Notifications</li>
        <li>Audit logs</li>
      </ul>
      <p>Trial: <strong id="modalTrialCode"></strong></p>
      <form method="post" id="deleteForm" class="delete-form">
        <input type="hidden" name="csrf_token" value="<?=h(csrf_token())?>">
        <p>
          <label for="confirm_token">Ketik <strong>PERMANENT</strong> untuk konfirmasi:</label>
          <input type="text" id="confirm_token" name="confirm_token" class="form-control"
                 placeholder="Ketik PERMANENT" autocomplete="off">
        </p>
        <div class="modal-actions">
          <button type="button" class="btn btn-light" onclick="closeDeleteModal()">Batal</button>
          <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>Delete Permanently</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function showDeleteModal(id, code) {
  document.getElementById('modalTrialCode').textContent = code;
  document.getElementById('deleteForm').action = '/admin/trash/delete/' + id;
  document.getElementById('deleteModal').style.display = 'flex';
  document.getElementById('confirm_token').value = '';
  document.getElementById('confirmDeleteBtn').disabled = true;
}

function closeDeleteModal() {
  document.getElementById('deleteModal').style.display = 'none';
}

document.getElementById('confirm_token').addEventListener('input', function() {
  document.getElementById('confirmDeleteBtn').disabled = this.value !== 'PERMANENT';
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
  if (e.target === this) closeDeleteModal();
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeDeleteModal();
});
</script>

<style>
.page-header {
  margin-bottom: 20px;
}
.page-header h2 {
  margin: 0 0 5px 0;
  color: #333;
}
.page-header .subtitle {
  color: #666;
  margin: 0;
}
.filter-bar {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}
.filter-form {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  align-items: center;
}
.filter-form .form-control {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}
.filter-form input[type="text"] {
  flex: 1;
  min-width: 150px;
}
.filter-form input[type="date"] {
  width: 150px;
}
.info-bar {
  padding: 10px 0;
  color: #666;
  font-size: 14px;
}
.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: #f8f9fa;
  border-radius: 8px;
}
.empty-icon {
  font-size: 48px;
  margin-bottom: 10px;
}
.empty-state p {
  color: #666;
  margin: 0;
}
.data-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.data-table th {
  background: #f8f9fa;
  padding: 12px 15px;
  text-align: left;
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #e9ecef;
}
.data-table td {
  padding: 12px 15px;
  border-bottom: 1px solid #e9ecef;
}
.data-table tr:hover {
  background: #f8f9fa;
}
.text-muted {
  color: #666;
}
.action-cell {
  white-space: nowrap;
}
.action-buttons {
  display: flex;
  gap: 8px;
}
.btn-sm {
  padding: 5px 10px;
  font-size: 13px;
  border-radius: 4px;
  border: none;
  cursor: pointer;
}
.btn-success {
  background: #28a745;
  color: white;
}
.btn-success:hover {
  background: #218838;
}
.btn-danger {
  background: #dc3545;
  color: white;
}
.btn-danger:hover {
  background: #c82333;
}
.btn-light {
  background: #e9ecef;
  color: #333;
}
.btn-secondary {
  background: #6c757d;
  color: white;
}
.badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}
.status-draft { background: #e9ecef; color: #495057; }
.status-review { background: #cce5ff; color: #004085; }
.status-ready { background: #fff3cd; color: #856404; }
.status-approved { background: #d4edda; color: #155724; }
.status-revision { background: #fff3cd; color: #856404; }
.status-rejected { background: #f8d7da; color: #721c24; }

/* Modal Styles */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow: auto;
}
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #e9ecef;
}
.modal-header h3 {
  margin: 0;
  color: #dc3545;
}
.modal-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #666;
}
.modal-body {
  padding: 20px;
}
.warning-text {
  color: #dc3545;
  font-size: 14px;
}
.modal-body ul {
  padding-left: 20px;
  color: #666;
  font-size: 14px;
}
.delete-form label {
  display: block;
  margin-bottom: 8px;
  color: #333;
}
.delete-form .form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}
.inline-form {
  display: inline;
}
</style>