<?php
$currentPath=normalize_request_path(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));
$isLogin=$name==='login';
$pageMap=[
 '/dashboard'=>'Trial Dashboard',
 '/trials/create'=>'New Trial',
 '/trials/approved'=>'Approved Trials',
 '/trials/in-review'=>'In Review Trials',
 '/trials/need-revision'=>'Need Revision Trials',
 '/trials/rejected'=>'Rejected Trials',
 '/trials/waiting-approval'=>'Waiting Approval',
 '/approval'=>'Approval',
 '/approvals'=>'Approval',
 '/report'=>'Report',
 '/settings/users'=>'Settings',
 '/templates/master'=>'Master Template',
 '/templates/parameters'=>'Parameter Template',
 '/templates/products'=>'Product Template',
 '/notifications'=>'Notifications',
 '/admin/notifications'=>'Admin Notifications',
 '/admin/activity-logs'=>'Activity Logs',
];
$topTitle=$pageTitle??($pageMap[$currentPath]??'Trial Validation System');
$isActive=function($paths)use($currentPath){
 foreach((array)$paths as $p){
  if($currentPath===$p||($p!=='/'&&str_starts_with($currentPath,$p.'/'))) return 'active';
 }
 return '';
};
$nav=[];
if(!$isLogin){
 $topNotifications=notifications_for_user(5);
 $topNotificationCount=unread_notifications_count();
 $nav[]=['label'=>'Dashboard','href'=>'/dashboard','icon'=>'D','paths'=>['/dashboard']];
 if(is_admin()||role()==='Staff') $nav[]=['label'=>'New Trial','href'=>'/trials/create','icon'=>'N','paths'=>['/trials/create']];
 if(can_approve_trials()) $nav[]=['label'=>'Waiting Approval','href'=>'/trials/waiting-approval','icon'=>'W','paths'=>['/trials/waiting-approval']];
 $nav[]=['label'=>'In Review','href'=>'/trials/in-review','icon'=>'R','paths'=>['/trials/in-review','/reviews']];
 $nav[]=['label'=>'Approved','href'=>'/trials/approved','icon'=>'A','paths'=>['/trials/approved']];
 $nav[]=['label'=>'Need Revision','href'=>'/trials/need-revision','icon'=>'V','paths'=>['/trials/need-revision']];
 $nav[]=['label'=>'Rejected','href'=>'/trials/rejected','icon'=>'X','paths'=>['/trials/rejected']];
 if(is_admin()||role()==='Staff'){
  $children=[];
  if(can_manage_master()) $children[]=['label'=>'Master','href'=>'/templates/master','paths'=>['/templates/master','/admin/masters']];
  if(can_manage_parameters()) $children[]=['label'=>'Parameters','href'=>'/templates/parameters','paths'=>['/templates/parameters','/admin/parameters']];
  if(can_view_products_template()) $children[]=['label'=>'Products','href'=>'/templates/products','paths'=>['/templates/products','/admin/products']];
  $nav[]=['label'=>'Templates','href'=>'#','icon'=>'T','paths'=>['/templates','/admin/masters','/admin/parameters','/admin/products'],'children'=>$children];
 }
 if(can_approve_trials()) $nav[]=['label'=>'Approval','href'=>'/approval','icon'=>'Q','paths'=>['/approval','/approvals']];
 $nav[]=['label'=>'Report','href'=>'/report','icon'=>'P','paths'=>['/report'],'children'=>[
  ['label'=>'Approved Report','href'=>'/report/approved','paths'=>['/report/approved']],
  ['label'=>'Rejected Report','href'=>'/report/rejected','paths'=>['/report/rejected']],
  ['label'=>'Trial Summary Report','href'=>'/report/trial-summary','paths'=>['/report/trial-summary']],
  ['label'=>'Department Review Report','href'=>'/report/department-review','paths'=>['/report/department-review']],
  ['label'=>'Audit Print Log','href'=>'/report/audit-print-log','paths'=>['/report/audit-print-log']],
 ]];
 if(is_admin()) $nav[]=['label'=>'Settings','href'=>'/settings/users','icon'=>'S','paths'=>['/settings','/admin/users','/admin/notifications','/admin/activity-logs','/admin/trash'],'children'=>[
  ['label'=>'Users','href'=>'/settings/users','paths'=>['/settings/users','/admin/users']],
  ['label'=>'Notifications','href'=>'/admin/notifications','paths'=>['/admin/notifications']],
  ['label'=>'Activity Logs','href'=>'/admin/activity-logs','paths'=>['/admin/activity-logs']],
  ['label'=>'Trash','href'=>'/admin/trash','paths'=>['/admin/trash']],
 ]];
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?=h($topTitle)?> - Trial Validation System</title>
  <link rel="stylesheet" href="/style.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body class="<?=$isLogin?'login-page':'app-shell'?>">
<?php if($isLogin): ?>
  <main class="login-main"><?php flash(); partial($name,get_defined_vars()); ?></main>
<?php else: ?>
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-mark">TV</div>
      <div>
        <div class="brand-name">Trial Validation</div>
        <div class="brand-caption">QAC System</div>
      </div>
    </div>
    <nav class="side-nav">
      <?php foreach($nav as $item): $active=$isActive($item['paths']??[$item['href']]); ?>
        <div class="nav-group <?=$active?>">
          <?php if(empty($item['children'])): ?>
            <a href="<?=$item['href']?>" class="side-link <?=$active?>">
              <span class="nav-icon"><?=h($item['icon'])?></span>
              <span><?=h($item['label'])?></span>
            </a>
          <?php else: ?>
            <div class="side-link nav-parent <?=$active?>">
              <span class="nav-icon"><?=h($item['icon'])?></span>
              <span><?=h($item['label'])?></span>
            </div>
            <div class="submenu">
              <?php foreach($item['children'] as $child): ?>
                <a href="<?=$child['href']?>" class="submenu-link <?=$isActive($child['paths'])?>"><?=h($child['label'])?></a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </nav>
    <a href="/logout" class="side-link logout-side"><span class="nav-icon">L</span><span>Logout</span></a>
  </aside>
  <div class="app-main">
    <header class="topbar">
      <button class="menu-toggle" type="button" onclick="document.body.classList.toggle('nav-open')">Menu</button>
      <div>
        <div class="top-title"><?=h($topTitle)?></div>
        <div class="top-subtitle">Trial Validation System</div>
      </div>
      <div class="user-chip">
        <div class="notification-menu">
          <button class="notification-bell" type="button" aria-label="Notifications" onclick="document.body.classList.toggle('notif-open')">
            <span>&#128276;</span>
            <?php if($topNotificationCount>0): ?><b id="notification-count"><?=h($topNotificationCount)?></b><?php endif; ?>
          </button>
          <div class="notification-dropdown">
            <div class="notification-head">
              <strong>Notifications</strong>
              <?php if($topNotificationCount>0): ?><span><?=h($topNotificationCount)?> unread</span><?php endif; ?>
            </div>
            <div class="notification-items">
              <?php foreach($topNotifications as $notif): ?>
                <a class="notification-item <?=$notif['is_read']?'':'unread'?>" href="/notifications/read/<?=$notif['id']?>">
                  <div>
                    <strong><?=h($notif['title'])?></strong>
                    <p><?=h(strlen($notif['message'])>92?substr($notif['message'],0,92).'...':$notif['message'])?></p>
                    <small><?=h($notif['created_at'])?></small>
                  </div>
                  <span class="type-badge <?=h(notification_type_class($notif['type']))?>"><?=h($notif['type'])?></span>
                </a>
              <?php endforeach; ?>
              <?php if(!$topNotifications): ?><div class="notification-empty">Belum ada notifikasi.</div><?php endif; ?>
            </div>
            <a class="notification-all" href="/notifications">View All Notifications</a>
          </div>
        </div>
        <div class="user-meta">
          <strong><?=h(u()['name']??u()['email']??'User')?></strong>
          <span><?=h(role_label())?></span>
        </div>
        <div class="avatar"><?=h(user_initials())?></div>
      </div>
    </header>
    <main class="content-area"><?php flash(); partial($name,get_defined_vars()); ?></main>
  </div>
<?php endif; ?>
<script>
$(function(){ $('.select2').select2({width:'100%'}); });
(function(){
  document.addEventListener('click', function(e){
    if(!e.target.closest('.notification-menu')) document.body.classList.remove('notif-open');
  });
  const badge=document.getElementById('notification-count');
  if(badge){
    setInterval(function(){
      fetch('/api/notifications/unread-count',{headers:{'Accept':'application/json'}})
        .then(r=>r.ok?r.json():null)
        .then(data=>{ if(data&&typeof data.count!=='undefined') badge.textContent=data.count; })
        .catch(()=>{});
    },30000);
  }
})();
(function(){
  const modal = document.createElement('div');
  modal.className = 'image-modal';
  modal.innerHTML = '<button class="image-modal-close" type="button">x</button><div class="image-modal-tools"><button class="image-modal-zoom-out" type="button">-</button><button class="image-modal-reset" type="button">Reset</button><button class="image-modal-zoom-in" type="button">+</button></div><button class="image-modal-prev" type="button">&lt;</button><div class="image-modal-stage"><img alt="Preview"></div><button class="image-modal-next" type="button">&gt;</button><div class="image-modal-caption"></div>';
  document.body.appendChild(modal);
  let list = [], index = 0, zoom = 1;
  const img = modal.querySelector('img');
  const caption = modal.querySelector('.image-modal-caption');
  function applyZoom(){
    img.style.transform = 'scale(' + zoom + ')';
    img.style.transformOrigin = 'center center';
    modal.classList.toggle('zoomed', zoom > 1);
  }
  function setZoom(next){
    zoom = Math.max(1, Math.min(4, next));
    applyZoom();
  }
  function openAt(i){
    index = i;
    const el = list[index];
    img.src = el.getAttribute('src');
    caption.textContent = el.getAttribute('data-caption') || '';
    setZoom(1);
    modal.classList.add('show');
  }
  function close(){ modal.classList.remove('show','zoomed'); img.src=''; setZoom(1); }
  function move(step){ if(!list.length) return; openAt((index + step + list.length) % list.length); }
  document.addEventListener('click', function(e){
    const target = e.target.closest('.photo-preview');
    if(target){
      e.preventDefault();
      list = Array.from(document.querySelectorAll('.photo-preview'));
      openAt(list.indexOf(target));
    }
  });
  modal.querySelector('.image-modal-close').onclick = close;
  modal.querySelector('.image-modal-zoom-in').onclick = function(e){e.stopPropagation();setZoom(zoom + .5)};
  modal.querySelector('.image-modal-zoom-out').onclick = function(e){e.stopPropagation();setZoom(zoom - .5)};
  modal.querySelector('.image-modal-reset').onclick = function(e){e.stopPropagation();setZoom(1)};
  img.addEventListener('dblclick', function(e){e.stopPropagation();setZoom(zoom > 1 ? 1 : 2)});
  img.addEventListener('wheel', function(e){e.preventDefault();e.stopPropagation();setZoom(zoom + (e.deltaY < 0 ? .25 : -.25));}, {passive:false});
  modal.querySelector('.image-modal-prev').onclick = function(e){e.stopPropagation();move(-1)};
  modal.querySelector('.image-modal-next').onclick = function(e){e.stopPropagation();move(1)};
  modal.addEventListener('click', function(e){ if(e.target===modal) close(); });
  document.addEventListener('keydown', function(e){
    if(!modal.classList.contains('show')) return;
    if(e.key==='Escape') close();
    if(e.key==='ArrowLeft') move(-1);
    if(e.key==='ArrowRight') move(1);
    if(e.key==='+'||e.key==='=') setZoom(zoom + .5);
    if(e.key==='-') setZoom(zoom - .5);
    if(e.key==='0') setZoom(1);
  });
})();
</script>
</body>
</html>
