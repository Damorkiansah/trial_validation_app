<?php


require __DIR__.'/../app/bootstrap.php';

$path=normalize_request_path(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));
$method=$_SERVER['REQUEST_METHOD'];

if($method==='POST') verify_csrf();

if($path==='/login'){
 if($method==='POST'){
  $s=db()->prepare('SELECT * FROM users WHERE email=? AND is_active=1');
  $s->execute([$_POST['email']??'']);
  $user=$s->fetch();
  if($user&&password_verify($_POST['password']??'',$user['password_hash'])){
   session_regenerate_id(true);
   unset($user['password_hash']);
   $_SESSION['user']=$user;
   logActivity('LOGIN','AUTH',$user['id'],$user['email']);
   redirect('/');
  }
  flash('Email atau password salah.');
 }
 view('login');
 exit;
}

if($path==='/logout'){
 logActivity('LOGOUT','AUTH',u()['id']??null,u()['email']??'');
 session_destroy();
 redirect('/login');
}

require_login();

if($path==='/'){
 redirect('/dashboard');
}

if($path==='/notifications'){
 $filter=$_GET['filter']??'all';
 if(!in_array($filter,['all','unread','read'],true)) $filter='all';
 $pagination=pagination_state(notifications_for_user_count($filter));
 $items=notifications_for_user($pagination['per_page'],$filter,$pagination['offset']);
 view('notifications',compact('items','filter','pagination'));
 exit;
}

if(preg_match('#^/notifications/read/(\d+)$#',$path,$m)){
 $id=(int)$m[1];
 [$scope,$params]=notification_scope_sql('n');
 $s=db()->prepare('SELECT n.*,h.progress_status,h.current_step FROM notifications n LEFT JOIN trials_header h ON h.id=n.trial_id WHERE n.id=? AND '.$scope.' LIMIT 1');
 $s->execute(array_merge([$id],$params));
 $notification=$s->fetch();
 if(!$notification) die('Notification not found');
 setNotificationStatus($id,1,null);
 logActivity('MARK_NOTIFICATION_READ','NOTIFICATION',$id,$notification['title']??'',null,$notification);
 redirect(notification_link($notification));
}

if($path==='/notifications/read-all'){
 if($method==='POST'){
  [$scope,$params]=notification_scope_sql('n');
  $ids=db()->prepare('SELECT n.id FROM notifications n LEFT JOIN notification_user_status ns ON ns.notification_id=n.id AND ns.user_id=? WHERE '.$scope.' AND COALESCE(ns.is_removed,n.removed_by_user,0)=0 AND COALESCE(ns.is_read,n.is_read,0)=0');
  $ids->execute(array_merge([(int)u()['id']],$params));
  foreach($ids->fetchAll(PDO::FETCH_COLUMN) as $nid) setNotificationStatus($nid,1,null);
  logActivity('MARK_ALL_NOTIFICATION_READ','NOTIFICATION',u()['id']??null,user_display_name());
 }
 redirect('/notifications');
}

if(preg_match('#^/notifications/remove/(\d+)$#',$path,$m)){
 if($method!=='POST') redirect('/notifications');
 $id=(int)$m[1];
 [$scope,$params]=notification_scope_sql('n');
 $s=db()->prepare('SELECT n.* FROM notifications n WHERE n.id=? AND '.$scope.' LIMIT 1');
 $s->execute(array_merge([$id],$params));
 $notification=$s->fetch();
 if(!$notification) die('Notification not found');
 setNotificationStatus($id,1,1);
 logActivity('REMOVE_NOTIFICATION','NOTIFICATION',$id,$notification['title']??'',$notification,null);
 redirect('/notifications');
}

if($path==='/notifications/unread-count'||$path==='/api/notifications/unread-count'){
 header('Content-Type: application/json');
 echo json_encode(['count'=>unread_notifications_count()]);
 exit;
}

if($path==='/api/notifications/latest'){
 header('Content-Type: application/json');
 echo json_encode(['items'=>notifications_for_user(5)]);
 exit;
}

if($path==='/admin/notifications'){
 if(!is_admin()) die('403 Forbidden');
 $total=(int)db()->query('SELECT COUNT(*) FROM notifications')->fetchColumn();
 $pagination=pagination_state($total);
 $s=db()->prepare('SELECT n.*,u.name AS user_name,u.email AS user_email,h.trial_code FROM notifications n LEFT JOIN users u ON u.id=n.user_id LEFT JOIN trials_header h ON h.id=n.trial_id ORDER BY n.created_at DESC,n.id DESC LIMIT ? OFFSET ?');
 $s->bindValue(1,$pagination['per_page'],PDO::PARAM_INT);
 $s->bindValue(2,$pagination['offset'],PDO::PARAM_INT);
 $s->execute();
 $items=$s->fetchAll();
 view('admin_notifications',compact('items','pagination'));
 exit;
}

if(preg_match('#^/admin/notifications/delete/(\d+)$#',$path,$m)){
 if(!is_admin()) die('403 Forbidden');
 if($method!=='POST') redirect('/admin/notifications');
 $id=(int)$m[1];
 $s=db()->prepare('SELECT * FROM notifications WHERE id=?');
 $s->execute([$id]);
 $old=$s->fetch();
 if($old){
  db()->prepare('DELETE FROM notification_user_status WHERE notification_id=?')->execute([$id]);
  db()->prepare('DELETE FROM notifications WHERE id=?')->execute([$id]);
  logActivity('DELETE_NOTIFICATION','NOTIFICATION',$id,$old['title']??'',$old,null);
 }
 redirect('/admin/notifications');
}

if($path==='/admin/activity-logs'){
 if(!is_admin()) die('403 Forbidden');
 $filters=[
  'date_from'=>trim($_GET['date_from']??''),
  'date_to'=>trim($_GET['date_to']??''),
  'user'=>trim($_GET['user']??''),
  'role'=>trim($_GET['role']??''),
  'module'=>trim($_GET['module']??''),
  'action'=>trim($_GET['action']??''),
  'q'=>trim($_GET['q']??''),
 ];
 $where=[];$params=[];
 if($filters['date_from']!==''&&preg_match('/^\d{4}-\d{2}-\d{2}$/',$filters['date_from'])){$where[]='created_at>=?';$params[]=$filters['date_from'].' 00:00:00';}
 if($filters['date_to']!==''&&preg_match('/^\d{4}-\d{2}-\d{2}$/',$filters['date_to'])){$where[]='created_at<=?';$params[]=$filters['date_to'].' 23:59:59';}
 if($filters['user']!==''){$where[]='user_name LIKE ?';$params[]='%'.$filters['user'].'%';}
 if($filters['role']!==''){$where[]='user_role=?';$params[]=$filters['role'];}
 if($filters['module']!==''){$where[]='module=?';$params[]=$filters['module'];}
 if($filters['action']!==''){$where[]='action=?';$params[]=$filters['action'];}
 if($filters['q']!==''){$where[]='(record_label LIKE ? OR old_data LIKE ? OR new_data LIKE ?)';$like='%'.$filters['q'].'%';array_push($params,$like,$like,$like);}
 $countSql='SELECT COUNT(*) FROM activity_logs'.($where?' WHERE '.implode(' AND ',$where):'');
 $c=db()->prepare($countSql);$c->execute($params);
 $pagination=pagination_state((int)$c->fetchColumn());
 $sql='SELECT * FROM activity_logs'.($where?' WHERE '.implode(' AND ',$where):'').' ORDER BY created_at DESC,id DESC LIMIT ? OFFSET ?';
 $s=db()->prepare($sql);
 $i=1;foreach($params as $p) $s->bindValue($i++,$p);
 $s->bindValue($i++,$pagination['per_page'],PDO::PARAM_INT);
 $s->bindValue($i++,$pagination['offset'],PDO::PARAM_INT);
 $s->execute();
 $items=$s->fetchAll();
 view('activity_logs',compact('items','filters','pagination'));
 exit;
}

if(preg_match('#^/admin/activity-logs/delete/(\d+)$#',$path,$m)){
 if(!is_admin()) die('403 Forbidden');
 if($method!=='POST') redirect('/admin/activity-logs');
 $id=(int)$m[1];
 $s=db()->prepare('SELECT * FROM activity_logs WHERE id=?');
 $s->execute([$id]);
 $old=$s->fetch();
 if($old){
  db()->prepare('DELETE FROM activity_logs WHERE id=?')->execute([$id]);
 }
 redirect('/admin/activity-logs');
}

if($path==='/admin/activity-logs/delete-selected'){
 if(!is_admin()) die('403 Forbidden');
 if($method!=='POST') redirect('/admin/activity-logs');
 $ids=array_values(array_unique(array_filter(array_map('intval',$_POST['log_ids']??[]),fn($id)=>$id>0)));
 if($ids){
  $placeholders=implode(',',array_fill(0,count($ids),'?'));
  $s=db()->prepare("DELETE FROM activity_logs WHERE id IN ($placeholders)");
  $s->execute($ids);
 }
 redirect('/admin/activity-logs');
}

if($path==='/dashboard'){
 $filters=[
  'q'=>trim($_GET['q']??''),
  'product_type'=>trim($_GET['product_type']??''),
  'date_from'=>trim($_GET['date_from']??''),
  'date_to'=>trim($_GET['date_to']??''),
  'status'=>trim($_GET['status']??''),
 ];
 $pagination=pagination_state(scoped_trials_count($filters));
 $trials=scoped_trials_query($filters,null,$pagination['per_page'],$pagination['offset']);
 $summary=trial_summary_counts();
 $productTypes=opts('product_type');
 view('dashboard',compact('trials','filters','productTypes','summary','pagination'));
 exit;
}

if(in_array($path,['/trials/approved','/trials/in-review','/trials/need-revision','/trials/rejected','/trials/waiting-approval'],true)){
 $map=[
  '/trials/approved'=>['approved','Approved Trials','Daftar trial dengan status approved.'],
  '/trials/in-review'=>['in-review','In Review Trials','Trial yang sedang dalam proses review.'],
  '/trials/need-revision'=>['need-revision','Need Revision Trials','Trial yang dikembalikan ke Staff untuk direvisi.'],
  '/trials/rejected'=>['rejected','Rejected Trials','Trial yang ditolak final.'],
  '/trials/waiting-approval'=>['waiting','Waiting Approval','Trial yang menunggu approval Manager QAC.'],
 ];
 [$group,$pageTitle,$pageSubtitle]=$map[$path];
 $filters=[
  'q'=>trim($_GET['q']??''),
  'product_type'=>trim($_GET['product_type']??''),
  'date_from'=>trim($_GET['date_from']??''),
  'date_to'=>trim($_GET['date_to']??''),
 ];
 $pagination=pagination_state(scoped_trials_count($filters,$group));
 $trials=scoped_trials_query($filters,$group,$pagination['per_page'],$pagination['offset']);
 $productTypes=opts('product_type');
 view('trials_list',compact('trials','filters','productTypes','pageTitle','pageSubtitle','group','pagination'));
 exit;
}

if($path==='/report'){
 view('report_index');
 exit;
}

if($path==='/report/approved'){
 $pagination=pagination_state(scoped_trials_count([], 'approved'));
 $items=scoped_trials_query([], 'approved',$pagination['per_page'],$pagination['offset']);
 view('report_approved',compact('items','pagination'));
 exit;
}

if($path==='/report/rejected'){
 $pagination=pagination_state(scoped_trials_count([], 'rejected'));
 $items=scoped_trials_query([], 'rejected',$pagination['per_page'],$pagination['offset']);
 view('report_rejected',compact('items','pagination'));
 exit;
}

if($path==='/report/trial-summary'){
 $filters=[
  'date_from'=>trim($_GET['date_from']??''),
  'date_to'=>trim($_GET['date_to']??''),
  'status'=>trim($_GET['status']??''),
  'product_type'=>trim($_GET['product_type']??''),
  'validation_scope'=>trim($_GET['validation_scope']??''),
  'machine_used'=>trim($_GET['machine_used']??''),
  'product_name'=>trim($_GET['product_name']??''),
 ];
 $pagination=pagination_state(scoped_trials_count($filters));
 $items=scoped_trials_query($filters,null,$pagination['per_page'],$pagination['offset']);
 $productTypes=opts('product_type');
 $validationScopes=opts('validation_scope');
 $machines=opts('machine_used');
 view('report_trial_summary',compact('items','filters','productTypes','validationScopes','machines','pagination'));
 exit;
}

if($path==='/report/department-review'){
 $from='trials_header h LEFT JOIN trials_review tr ON tr.trial_id=h.id AND tr.review_round=h.revision_no+1';
 $where=[];
 $params=[];
 if(is_reviewer()){
  $departments=review_departments_for_user();
  $where[]='EXISTS (SELECT 1 FROM trials_review tr2 WHERE tr2.trial_id=h.id AND tr2.review_round=h.revision_no+1 AND UPPER(TRIM(tr2.department)) IN ('.implode(',',array_fill(0,count($departments),'?')).'))';
  $params=array_merge($params,$departments);
 }
 $countSql='SELECT COUNT(DISTINCT h.id) FROM '.$from;
 if($where) $countSql.=' WHERE '.implode(' AND ',$where);
 $cs=db()->prepare($countSql);
 $cs->execute($params);
 $pagination=pagination_state((int)$cs->fetchColumn());
 $reviewDepartments=reviewer_department_codes();
 $selectDepartments=[];
 foreach($reviewDepartments as $dept){
  $alias='dept_'.preg_replace('/[^A-Z0-9_]/','_',normalize_department($dept));
  $selectDepartments[]='COALESCE(MAX(CASE WHEN UPPER(TRIM(tr.department))='.db()->quote(normalize_department($dept)).' THEN tr.status END),"N/A") AS '.$alias;
 }
 $departmentSql=$selectDepartments?','.implode(',',$selectDepartments):'';
 $sql='SELECT h.id,h.trial_code,h.product_name,h.pending_with'.$departmentSql.',h.progress_status FROM '.$from;
 if($where) $sql.=' WHERE '.implode(' AND ',$where);
 $sql.=' GROUP BY h.id,h.trial_code,h.product_name,h.pending_with,h.progress_status ORDER BY h.updated_at DESC,h.id DESC LIMIT '.(int)$pagination['per_page'].' OFFSET '.(int)$pagination['offset'];
 $s=db()->prepare($sql);
 $s->execute($params);
 $items=$s->fetchAll();
 view('report_department_review',compact('items','pagination','reviewDepartments'));
 exit;
}

if($path==='/report/audit-print-log'){
 $items=[];
 $pagination=pagination_state(0);
 try{
  $c=db()->prepare('SELECT COUNT(*) FROM audit_logs WHERE action=?');
  $c->execute(['report_printed']);
  $pagination=pagination_state((int)$c->fetchColumn());
  $s=db()->prepare('SELECT trial_id,user_email AS printed_by,created_at AS printed_at,new_data FROM audit_logs WHERE action=? ORDER BY created_at DESC LIMIT ? OFFSET ?');
  $s->bindValue(1,'report_printed');
  $s->bindValue(2,$pagination['per_page'],PDO::PARAM_INT);
  $s->bindValue(3,$pagination['offset'],PDO::PARAM_INT);
  $s->execute();
  $items=$s->fetchAll();
 }catch(Exception $e){}
 view('report_audit_print_log',compact('items','pagination'));
 exit;
}

if(preg_match('#^/trials/(\d+)/print-log$#',$path,$m)&&$method==='POST'){
 $t=trial($m[1]);
 require_trial_view($t);
 audit_log($t['id'],'report_printed',[],['report_type'=>'Report Summary']);
 logActivity('PRINT_REPORT','REPORT',$t['id'],$t['trial_code'],null,['report_type'=>'Report Summary']);
 header('Content-Type: application/json');
 echo json_encode(['ok'=>true]);
 exit;
}

if($path==='/trials/create'){
 if(!is_staff()) die('Tidak boleh membuat trial.');
 $products=db()->query('SELECT * FROM products WHERE is_active=1 ORDER BY product_name')->fetchAll();
 view('trial_form',['trial'=>null,'products'=>$products]);
 exit;
}

if($path==='/trials/store'&&$method==='POST'){
 if(!is_staff()) die('Forbidden');
 $validationScope=multi_values($_POST['validation_scope']??[]);
 $machineUsed=multi_values($_POST['machine_used']??[]);
 $required=['product_id','product_type','validation_date','validation_category','risk_level','estimate_qty','batch_number','bulk_code','support_team','initiated_person_team','reason','bom'];
 foreach($required as $r){
  if(trim($_POST[$r]??'')===''){
   $_SESSION['old']=$_POST;
   flash('Field wajib belum lengkap. Data tidak direset.');
   redirect('/trials/create');
  }
 }
 if(!$validationScope){
  $_SESSION['old']=$_POST;
  flash('Please select at least one validation scope.');
  redirect('/trials/create');
 }
 if(!$machineUsed){
  $_SESSION['old']=$_POST;
  flash('Please select at least one machine.');
  redirect('/trials/create');
 }
 if(!is_numeric($_POST['estimate_qty'])||(float)$_POST['estimate_qty']<0){
  $_SESSION['old']=$_POST;
  flash('Estimate Qty wajib angka dan tidak boleh minus.');
  redirect('/trials/create');
 }
 foreach(['product_type','validation_category'] as $type){
  if(!option_exists($type,$_POST[$type])){
   $_SESSION['old']=$_POST;
   flash('Master option tidak valid: '.$type.'.');
   redirect('/trials/create');
  }
 }
 if(!options_exist('validation_scope',$validationScope)||!options_exist('machine_used',$machineUsed)){
  $_SESSION['old']=$_POST;
  flash('Master option tidak valid untuk Validation Scope atau Machine Used.');
  redirect('/trials/create');
 }
 if(!in_array($_POST['risk_level'],['Low','Medium','High'],true)){
  $_SESSION['old']=$_POST;
  flash('Risk level tidak valid.');
  redirect('/trials/create');
 }
 $p=db()->prepare('SELECT * FROM products WHERE id=?');
 $p->execute([$_POST['product_id']]);
 $prod=$p->fetch();
 if(!$prod) die('Product invalid');

 $code='TRIAL-'.date('Ymd-His');
 db()->prepare('INSERT INTO trials_header(trial_code,product_id,product_name,finish_good_code,product_type,validation_date,validation_category,risk_level,validation_scope,machine_used,estimate_qty,batch_number,bulk_code,support_team,initiated_person_team,reason,bom,current_step,progress_status,created_by,updated_at,created_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())')
  ->execute([$code,$prod['id'],$prod['product_name'],$prod['finish_good_code'],$_POST['product_type'],$_POST['validation_date'],$_POST['validation_category'],$_POST['risk_level'],encode_multi_value($validationScope),encode_multi_value($machineUsed),$_POST['estimate_qty'],$_POST['batch_number'],$_POST['bulk_code'],$_POST['support_team'],$_POST['initiated_person_team'],$_POST['reason'],$_POST['bom'],'Validation','Draft',u()['email'],date('Y-m-d H:i:s')]);
 $trial_id=db()->lastInsertId();
 audit_log($trial_id,'trial_created',[],['trial_code'=>$code,'product'=>$prod['product_name']]);
 logActivity('CREATE','TRIAL',$trial_id,$code,null,['trial_code'=>$code,'product'=>$prod['product_name']]);
 redirect('/trials/'.$trial_id.'/validation');
}

if(preg_match('#^/trials/(\d+)/edit$#',$path,$m)){
 $t=trial($m[1]);
 if(!can_edit($t)) die('Trial sudah terkunci.');
 $products=db()->query('SELECT * FROM products WHERE is_active=1 ORDER BY product_name')->fetchAll();
 view('trial_form',['trial'=>$t,'products'=>$products]);
 exit;
}

if(preg_match('#^/trials/(\d+)/update$#',$path,$m)&&$method==='POST'){
 $t=trial($m[1]);
 if(!can_edit($t)) die('Trial sudah terkunci.');
 $validationScope=multi_values($_POST['validation_scope']??[]);
 $machineUsed=multi_values($_POST['machine_used']??[]);
 $required=['product_id','product_type','validation_date','validation_category','risk_level','estimate_qty','batch_number','bulk_code','support_team','initiated_person_team','reason','bom'];
 foreach($required as $r){
  if(trim($_POST[$r]??'')===''){
   $_SESSION['old']=$_POST;
   flash('Field wajib belum lengkap. Data tidak direset.');
   redirect('/trials/'.$t['id'].'/edit');
  }
 }
 if(!$validationScope){
  $_SESSION['old']=$_POST;
  flash('Please select at least one validation scope.');
  redirect('/trials/'.$t['id'].'/edit');
 }
 if(!$machineUsed){
  $_SESSION['old']=$_POST;
  flash('Please select at least one machine.');
  redirect('/trials/'.$t['id'].'/edit');
 }
 if(!is_numeric($_POST['estimate_qty'])||(float)$_POST['estimate_qty']<0){
  $_SESSION['old']=$_POST;
  flash('Estimate Qty wajib angka dan tidak boleh minus.');
  redirect('/trials/'.$t['id'].'/edit');
 }
 foreach(['product_type','validation_category'] as $type){
  if(!option_exists($type,$_POST[$type])){
   $_SESSION['old']=$_POST;
   flash('Master option tidak valid: '.$type.'.');
   redirect('/trials/'.$t['id'].'/edit');
  }
 }
 if(!options_exist('validation_scope',$validationScope)||!options_exist('machine_used',$machineUsed)){
  $_SESSION['old']=$_POST;
  flash('Master option tidak valid untuk Validation Scope atau Machine Used.');
  redirect('/trials/'.$t['id'].'/edit');
 }
 if(!in_array($_POST['risk_level'],['Low','Medium','High'],true)){
  $_SESSION['old']=$_POST;
  flash('Risk level tidak valid.');
  redirect('/trials/'.$t['id'].'/edit');
 }
 $p=db()->prepare('SELECT * FROM products WHERE id=?');
 $p->execute([$_POST['product_id']]);
 $prod=$p->fetch();
 if(!$prod) die('Product invalid');

 db()->prepare('UPDATE trials_header SET product_id=?,product_name=?,finish_good_code=?,product_type=?,validation_date=?,validation_category=?,risk_level=?,validation_scope=?,machine_used=?,estimate_qty=?,batch_number=?,bulk_code=?,support_team=?,initiated_person_team=?,reason=?,bom=?,updated_at=NOW() WHERE id=?')
  ->execute([$prod['id'],$prod['product_name'],$prod['finish_good_code'],$_POST['product_type'],$_POST['validation_date'],$_POST['validation_category'],$_POST['risk_level'],encode_multi_value($validationScope),encode_multi_value($machineUsed),$_POST['estimate_qty'],$_POST['batch_number'],$_POST['bulk_code'],$_POST['support_team'],$_POST['initiated_person_team'],$_POST['reason'],$_POST['bom'],$t['id']]);
 audit_log($t['id'],'trial_header_updated',$t,[
  'product_id'=>$prod['id'],
  'product_type'=>$_POST['product_type'],
  'risk_level'=>$_POST['risk_level'],
  'validation_scope'=>$validationScope,
  'machine_used'=>$machineUsed,
  'batch_number'=>$_POST['batch_number'],
  'bulk_code'=>$_POST['bulk_code'],
  'support_team'=>$_POST['support_team'],
 'initiated_person_team'=>$_POST['initiated_person_team'],
 ]);
 logActivity('UPDATE','TRIAL',$t['id'],$t['trial_code'],$t,[
  'product_name'=>$prod['product_name'],
  'finish_good_code'=>$prod['finish_good_code'],
  'product_type'=>$_POST['product_type'],
  'validation_date'=>$_POST['validation_date'],
 ]);
 redirect('/trials/'.$t['id'].'/validation');
}

if(preg_match('#^/trials/(\d+)/delete$#',$path,$m)&&$method==='POST'){
 if(!is_admin()) die('403 Forbidden');
 $t=trial($m[1]);
 db()->prepare('UPDATE trials_header SET deleted_at=NOW(),deleted_by=?,updated_at=NOW() WHERE id=?')->execute([u()['id'],$t['id']]);
 logActivity('SOFT_DELETE','TRIAL',$t['id'],$t['trial_code'],$t,null);
 redirect('/dashboard');
}

if(preg_match('#^/trials/(\d+)/validation$#',$path,$m)){
 $t=trial($m[1]);
 require_trial_view($t);
 $params=params_for($t['product_type']);
 $r=db()->prepare('SELECT * FROM trials_results WHERE trial_id=?');
 $r->execute([$t['id']]);
 $rows=[];
 foreach($r->fetchAll() as $x) $rows[$x['parameter_id']]=$x;
 view('validation',compact('t','params','rows'));
 exit;
}

if(preg_match('#^/trials/(\d+)/validation/save$#',$path,$m)&&$method==='POST'){
 $t=trial($m[1]);
 if(!can_edit($t)) die('Locked');
 $params=params_for($t['product_type']);
 if(!$params){
  flash('Parameter validation untuk product type ini belum dikonfigurasi.');
  redirect('/trials/'.$t['id'].'/validation');
 }
 $posted_ids=$_POST['parameter_id']??[];
 $decisions=$_POST['decision']??[];
 $results=$_POST['result']??[];
 $remarks=$_POST['remark']??[];
 $input=[];
 foreach($posted_ids as $i=>$pid){
  $input[(int)$pid]=[
   'decision'=>$decisions[$i]??'',
   'result'=>trim($results[$i]??''),
   'remark'=>trim($remarks[$i]??''),
  ];
 }
 $errors=[];
 $to_save=[];
 foreach($params as $p){
  $pid=(int)$p['id'];
  $row=$input[$pid]??null;
  if(!$row){
   $errors[]='Parameter '.$p['parameter_name'].' belum terisi.';
   continue;
  }
  $dec=$row['decision'];
  $res=$row['result'];
  $rem=$row['remark'];
  if(!in_array($dec,['OK','NOT OK','N/A'],true)){
   $errors[]='Decision parameter '.$p['parameter_name'].' tidak valid.';
   continue;
  }
  if($dec==='N/A') $res='N/A';
  if($dec==='OK'&&$res==='') $res='Conform';
  if($dec==='NOT OK'&&($res===''||$rem==='')) $errors[]='Parameter '.$p['parameter_name'].' NOT OK wajib isi Result dan Remark.';
  $to_save[]=[$t['id'],$pid,$res,$dec,$rem];
 }
 if($errors){
  flash('Validation belum lengkap: '.implode(' | ',$errors));
  redirect('/trials/'.$t['id'].'/validation');
 }
 db()->beginTransaction();
 foreach($to_save as $row){
  db()->prepare('INSERT INTO trials_results(trial_id,parameter_id,result_value,decision,remark,updated_at) VALUES(?,?,?,?,?,NOW()) ON DUPLICATE KEY UPDATE result_value=VALUES(result_value),decision=VALUES(decision),remark=VALUES(remark),updated_at=NOW()')
   ->execute($row);
 }
 db()->prepare('UPDATE trials_header SET current_step="WeighingPackaging",updated_at=NOW() WHERE id=?')->execute([$t['id']]);
 db()->commit();
 audit_log($t['id'],'validation_saved',[],['parameters'=>count($to_save)]);
 redirect('/trials/'.$t['id'].'/weighing/Packaging');
}

if(preg_match('#^/trials/(\d+)/weighing/(Packaging|Filling)$#',$path,$m)){
 $t=trial($m[1]);
 require_trial_view($t);
 $section=$m[2];
 $s=db()->prepare('SELECT * FROM trials_weighing WHERE trial_id=? AND section=? ORDER BY item_no');
 $s->execute([$t['id'],$section]);
 $vals=[];
 $skip=false;
 foreach($s->fetchAll() as $x){
  $vals[$x['item_no']]=$x['weight_value'];
  $skip=$skip||(bool)$x['is_skipped'];
 }
 $oldWeighing=$_SESSION['old_weighing']??null;
 if($oldWeighing&&($oldWeighing['trial_id']??null)==$t['id']&&($oldWeighing['section']??null)===$section){
  $vals=$oldWeighing['vals']??$vals;
  $skip=!empty($oldWeighing['skip']);
  unset($_SESSION['old_weighing']);
 }
 view('weighing',compact('t','section','vals','skip'));
 exit;
}

if(preg_match('#^/trials/(\d+)/weighing/(Packaging|Filling)/save$#',$path,$m)&&$method==='POST'){
 $t=trial($m[1]);
 if(!can_edit($t)) die('Locked');
 $section=$m[2];
 $rows=[];
 if(isset($_POST['skip'])){
  $rows[]=[$t['id'],$section,1,null,1];
 }else{
  foreach(($_POST['w']??[]) as $itemNo=>$rawValue){
   $value=trim((string)$rawValue);
   if($value==='') continue;
   if(!ctype_digit((string)$itemNo)||!is_numeric($value)||(float)$value<0){
    $_SESSION['old_weighing']=['trial_id'=>$t['id'],'section'=>$section,'vals'=>$_POST['w']??[],'skip'=>false];
    flash('Weighing sample must be numeric and cannot be negative.');
    redirect('/trials/'.$t['id'].'/weighing/'.$section);
   }
   $rows[]=[$t['id'],$section,(int)$itemNo,$value,0];
  }
  if(!$rows){
   $_SESSION['old_weighing']=['trial_id'=>$t['id'],'section'=>$section,'vals'=>$_POST['w']??[],'skip'=>false];
   flash('Please input at least 1 weighing sample or click Skip.');
   redirect('/trials/'.$t['id'].'/weighing/'.$section);
  }
 }
 db()->beginTransaction();
 db()->prepare('DELETE FROM trials_weighing WHERE trial_id=? AND section=?')->execute([$t['id'],$section]);
 foreach($rows as $row){
  db()->prepare('INSERT INTO trials_weighing(trial_id,section,item_no,weight_value,is_skipped) VALUES(?,?,?,?,?)')->execute($row);
 }
 $next=$section==='Packaging'?'/trials/'.$t['id'].'/weighing/Filling':'/trials/'.$t['id'].'/attachments';
 db()->prepare('UPDATE trials_header SET current_step=?,updated_at=NOW() WHERE id=?')->execute([$section==='Packaging'?'WeighingFilling':'Attachment',$t['id']]);
 db()->commit();
 audit_log($t['id'],'weighing_saved',[],['section'=>$section,'skipped'=>isset($_POST['skip'])]);
 redirect($next);
}

if(preg_match('#^/trials/(\d+)/attachments$#',$path,$m)){
 $t=trial($m[1]);
 require_trial_view($t);
 $cats=opts('attachment_category');
 $s=db()->prepare('SELECT * FROM trial_attachment_files WHERE trial_id=? ORDER BY category,id');
 $s->execute([$t['id']]);
 $files=$s->fetchAll();
 view('attachments',compact('t','cats','files'));
 exit;
}

if(preg_match('#^/trials/(\d+)/attachments/upload$#',$path,$m)&&$method==='POST'){
 $t=trial($m[1]);
 if(!can_edit($t)) die('Locked');
 $cat=$_POST['category']??'';
 $valid_cats=array_column(opts('attachment_category'),'name');
 if(!in_array($cat,$valid_cats,true)){
  flash('Kategori attachment tidak valid.');
  redirect('/trials/'.$t['id'].'/attachments');
 }
 $files=$_FILES['photos']??null;
 $errors=[];
 $saved=0;
 $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
 $max_size=10*1024*1024;
 $dir=__DIR__.'/uploads/'.$t['id'];
 if(!is_dir($dir)&&!mkdir($dir,0755,true)){
  flash('Folder upload tidak bisa dibuat.');
  redirect('/trials/'.$t['id'].'/attachments');
 }
 if(!$files||!is_array($files['tmp_name']??null)){
  flash('Pilih minimal satu foto.');
  redirect('/trials/'.$t['id'].'/attachments');
 }
 $finfo=finfo_open(FILEINFO_MIME_TYPE);
 foreach($files['tmp_name'] as $i=>$tmp){
  $error=$files['error'][$i]??UPLOAD_ERR_NO_FILE;
  if($error===UPLOAD_ERR_NO_FILE) continue;
  if($error!==UPLOAD_ERR_OK){
   $errors[]='Upload file ke-'.($i+1).' gagal.';
   continue;
  }
  if(($files['size'][$i]??0)>$max_size){
   $errors[]='File '.$files['name'][$i].' melebihi 10 MB.';
   continue;
  }
  $mime=finfo_file($finfo,$tmp);
  if(!isset($allowed[$mime])){
   $errors[]='File '.$files['name'][$i].' bukan gambar yang diizinkan.';
   continue;
  }
  $name=bin2hex(random_bytes(16)).'.'.$allowed[$mime];
  if(!move_uploaded_file($tmp,$dir.'/'.$name)){
   $errors[]='File '.$files['name'][$i].' gagal disimpan.';
   continue;
  }
  db()->prepare('INSERT INTO trial_attachment_files(trial_id,category,file_name,file_path,uploaded_by,created_at) VALUES(?,?,?,?,?,NOW())')
   ->execute([$t['id'],$cat,$name,'/uploads/'.$t['id'].'/'.$name,u()['email']]);
 $saved++;
 }
 finfo_close($finfo);
 db()->prepare('UPDATE trials_header SET current_step="Attachment",updated_at=NOW() WHERE id=?')->execute([$t['id']]);
 if($saved) audit_log($t['id'],'attachments_uploaded',[],['category'=>$cat,'count'=>$saved]);
 if($saved) logActivity('CREATE','ATTACHMENT',$t['id'],$t['trial_code'],null,['category'=>$cat,'count'=>$saved]);
 if(!$saved&&$errors) flash(implode(' | ',$errors));
 elseif($errors) flash('Sebagian file gagal: '.implode(' | ',$errors));
 redirect('/trials/'.$t['id'].'/attachments');
}

if(preg_match('#^/trials/(\d+)/attachments/(\d+)/delete$#',$path,$m)&&$method==='POST'){
 $t=trial($m[1]);
 if(!can_edit($t)) die('Locked');
 $attachment_id=(int)$m[2];
 $s=db()->prepare('SELECT * FROM trial_attachment_files WHERE id=? AND trial_id=?');
 $s->execute([$attachment_id,$t['id']]);
 $file=$s->fetch();
 if(!$file){
  flash('Attachment tidak ditemukan.');
  redirect('/trials/'.$t['id'].'/attachments');
 }
 $uploadsRoot=realpath(__DIR__.'/uploads');
 $target=realpath(__DIR__.$file['file_path']);
 db()->beginTransaction();
 db()->prepare('DELETE FROM trial_attachment_files WHERE id=? AND trial_id=?')->execute([$attachment_id,$t['id']]);
 db()->commit();
 if($target&&$uploadsRoot&&str_starts_with($target,$uploadsRoot)&&is_file($target)){
  @unlink($target);
 }
 audit_log($t['id'],'attachment_deleted',$file,[]);
 logActivity('DELETE','ATTACHMENT',$attachment_id,$file['file_name']??'',$file,null);
 flash('Attachment berhasil dihapus.');
 redirect('/trials/'.$t['id'].'/attachments');
}

if(preg_match('#^/trials/(\d+)/report$#',$path,$m)){
 $t=trial($m[1]);
 require_trial_view($t);
 $r=db()->prepare('SELECT vr.*,vp.parameter_name,vp.specification FROM trials_results vr LEFT JOIN validation_parameters vp ON vp.id=vr.parameter_id WHERE vr.trial_id=? ORDER BY vp.sort_order,vp.id');
 $r->execute([$t['id']]);
 $results=$r->fetchAll();
 $w=db()->prepare('SELECT * FROM trials_weighing WHERE trial_id=? ORDER BY section,item_no');
 $w->execute([$t['id']]);
 $weigh=$w->fetchAll();
 $f=db()->prepare('SELECT * FROM trial_attachment_files WHERE trial_id=? ORDER BY category,id');
 $f->execute([$t['id']]);
 $files=$f->fetchAll();
 $rs=db()->prepare('SELECT * FROM trials_review WHERE trial_id=? ORDER BY review_round,department');
 $rs->execute([$t['id']]);
 $reviews=$rs->fetchAll();
 $approvers=db()->query('SELECT id,name,email,role FROM users WHERE is_active=1 AND deleted_at IS NULL ORDER BY role,name,email')->fetchAll();
 $selectedApprover=null;
 if(!empty($t['approver_user_id'])){
  $selectedApproverStmt=db()->prepare('SELECT id,name,email,role FROM users WHERE id=?');
  $selectedApproverStmt->execute([(int)$t['approver_user_id']]);
  $selectedApprover=$selectedApproverStmt->fetch();
 }
 $completeness=trial_completeness($t);
 view('report',compact('t','results','weigh','files','reviews','approvers','selectedApprover','completeness'));
 exit;
}

if(preg_match('#^/trials/(\d+)/submit-review$#',$path,$m)&&$method==='POST'){
 $t=trial($m[1]);
 if(!can_edit($t)) die('Locked');
 $errors=trial_completeness($t);
 if($errors){
  flash('Belum bisa submit review: '.implode(' | ',$errors));
 redirect('/trials/'.$t['id'].'/report');
 }
 $round=current_review_round($t);
 $allowedDepartments=reviewer_department_codes();
 $departments=array_values(array_unique(array_filter(array_map('normalize_department',$_POST['departments']??[]),fn($d)=>in_array($d,$allowedDepartments,true))));
 if(!$departments){
 flash('Please select at least one review department.');
  redirect('/trials/'.$t['id'].'/report');
 }
 $approverId=(int)($_POST['approver_user_id']??0);
 $approverStmt=db()->prepare('SELECT id,name,email,role FROM users WHERE id=? AND is_active=1 AND deleted_at IS NULL');
 $approverStmt->execute([$approverId]);
 $approver=$approverStmt->fetch();
 if(!$approver){
  flash('Please select a valid approver.');
  redirect('/trials/'.$t['id'].'/report');
 }
 db()->beginTransaction();
 foreach($departments as $d){
  db()->prepare('INSERT INTO trials_review(trial_id,department,review_round,status,is_required) VALUES(?,?,?,"Pending",1) ON DUPLICATE KEY UPDATE status="Pending",is_required=1,reviewer_name=NULL,reviewer_email=NULL,comment=NULL,reviewed_at=NULL')
   ->execute([$t['id'],$d,$round]);
 }
 db()->prepare('UPDATE trials_header SET progress_status="In Review",current_step="Review",pending_with=?,approver_user_id=?,updated_at=NOW() WHERE id=?')
  ->execute([implode(',',$departments),$approver['id'],$t['id']]);
 db()->commit();
 foreach($departments as $d){
  createNotification([
   'role_target'=>'Reviewer',
   'department_target'=>$d,
   'trial_id'=>$t['id'],
   'title'=>'New Trial Waiting for Review',
   'message'=>'Trial '.$t['trial_code'].' - '.$t['product_name'].' membutuhkan review department Anda.',
   'type'=>'review',
  ]);
 }
 createNotification([
  'role_target'=>'Admin',
  'trial_id'=>$t['id'],
  'title'=>'Trial Submitted for Review',
  'message'=>'Trial '.$t['trial_code'].' - '.$t['product_name'].' dikirim ke review department: '.implode(', ',$departments).'.',
  'type'=>'info',
 ]);
 audit_log($t['id'],'submitted_for_review',[],['round'=>$round,'departments'=>$departments]);
 audit_log($t['id'],'approval_assignee_selected',[],['approver_user_id'=>$approver['id'],'approver_email'=>$approver['email'],'approver_name'=>$approver['name']]);
 logActivity('SUBMIT_REVIEW','TRIAL',$t['id'],$t['trial_code'],null,['round'=>$round,'departments'=>$departments,'approver'=>$approver['email']]);
 logActivity('SELECT_REVIEW_DEPARTMENT','REVIEW',$t['id'],$t['trial_code'],null,['departments'=>$departments]);
 logActivity('SELECT_APPROVER','APPROVAL',$t['id'],$t['trial_code'],null,['approver'=>$approver['email']]);
 redirect('/trials/'.$t['id'].'/report');
}

if($path==='/reviews'){
 if(!is_reviewer()) die('Reviewer only');
 $departments=review_departments_for_user();
 $placeholders=implode(',',array_fill(0,count($departments),'?'));
 $c=db()->prepare("SELECT COUNT(*) FROM trials_review tr JOIN trials_header h ON h.id=tr.trial_id WHERE h.progress_status='In Review' AND tr.review_round=h.revision_no+1 AND UPPER(TRIM(tr.department)) IN ($placeholders)");
 $c->execute($departments);
 $pagination=pagination_state((int)$c->fetchColumn());
 $s=db()->prepare("SELECT tr.*,h.trial_code,h.product_name,h.revision_no,h.progress_status FROM trials_review tr JOIN trials_header h ON h.id=tr.trial_id WHERE h.progress_status='In Review' AND tr.review_round=h.revision_no+1 AND UPPER(TRIM(tr.department)) IN ($placeholders) ORDER BY tr.status=\"Pending\" DESC,tr.id DESC LIMIT ".(int)$pagination['per_page']." OFFSET ".(int)$pagination['offset']);
 $s->execute($departments);
 $items=$s->fetchAll();
 view('reviews',compact('items','pagination'));
 exit;
}

if(preg_match('#^/review/(\d+)/save$#',$path,$m)&&$method==='POST'){
 if(!is_reviewer()) die('Reviewer only');
 $rid=(int)$m[1];
 $s=db()->prepare('SELECT tr.*,h.revision_no,h.progress_status,h.trial_code,h.product_name FROM trials_review tr JOIN trials_header h ON h.id=tr.trial_id WHERE tr.id=?');
 $s->execute([$rid]);
 $review=$s->fetch();
 if(!$review||!in_array(normalize_department($review['department']),review_departments_for_user(),true)) die('Forbidden');
 $round=current_review_round($review);
 if($review['progress_status']!=='In Review'||(int)$review['review_round']!==$round||$review['status']!=='Pending') die('Review sudah tidak aktif.');
 $comment=trim($_POST['comment']??'');
 if($comment===''){
  flash('Comment review wajib diisi.');
  redirect('/reviews');
 }
 $reviewerName=user_display_name();
 db()->prepare('UPDATE trials_review SET status="Reviewed",reviewer_name=?,reviewer_email=?,comment=?,reviewed_at=NOW() WHERE id=? AND status="Pending"')
  ->execute([$reviewerName,u()['email'],$comment,$rid]);
 $p=db()->prepare('SELECT department FROM trials_review WHERE trial_id=? AND review_round=? AND status="Pending" ORDER BY department');
 $p->execute([$review['trial_id'],$round]);
 $pending=$p->fetchAll(PDO::FETCH_COLUMN);
 if(!$pending){
  $trialApproverStmt=db()->prepare('SELECT h.approver_user_id,u.name,u.email FROM trials_header h LEFT JOIN users u ON u.id=h.approver_user_id WHERE h.id=?');
  $trialApproverStmt->execute([$review['trial_id']]);
  $trialApprover=$trialApproverStmt->fetch();
  $pendingApprover=trim((string)($trialApprover['name']??''))?:($trialApprover['email']??'Manager QAC');
  db()->prepare('UPDATE trials_header SET progress_status="Ready for Approval",current_step="Approval",pending_with=?,updated_at=NOW() WHERE id=?')->execute([$pendingApprover,$review['trial_id']]);
  createNotification([
   'user_id'=>$trialApprover['approver_user_id']??null,
   'role_target'=>empty($trialApprover['approver_user_id'])?'Manager QAC':null,
   'trial_id'=>$review['trial_id'],
   'title'=>'Trial Waiting Final Approval',
   'message'=>'Trial '.$review['trial_code'].' - '.$review['product_name'].' sudah selesai direview dan menunggu final approval.',
   'type'=>'approval',
  ]);
  createNotification([
   'role_target'=>'Admin',
   'trial_id'=>$review['trial_id'],
   'title'=>'Trial Waiting Final Approval',
   'message'=>'Trial '.$review['trial_code'].' - '.$review['product_name'].' sudah selesai direview dan menunggu final approval.',
   'type'=>'approval',
  ]);
 }else{
  db()->prepare('UPDATE trials_header SET pending_with=?,updated_at=NOW() WHERE id=?')->execute([implode(',',$pending),$review['trial_id']]);
 }
 audit_log($review['trial_id'],'department_reviewed',[],['department'=>$review['department'],'round'=>$round,'reviewer_name'=>$reviewerName,'reviewer_email'=>u()['email']]);
 logActivity('SUBMIT_REVIEW','REVIEW',$rid,$review['trial_code'].' '.$review['department'],null,['department'=>$review['department'],'round'=>$round,'comment'=>$comment]);
 redirect('/reviews');
}

if($path==='/approvals'||$path==='/approval'){
 if(!can_approve_trials()) die('Forbidden');
 $approvalWhere=['progress_status="Ready for Approval"','h.deleted_at IS NULL'];
 $approvalParams=[];
 // Admin, Manager QAC, Team Leader, Part Leader, Team Leader QA: see all trials
 $canSeeAll = is_admin() || is_manager_qac() || in_array(role(), ['Team Leader', 'Part Leader', 'Team Leader QA'], true);
 if(!$canSeeAll){
  $approvalWhere[]='h.approver_user_id=?';
  $approvalParams[]=(int)(u()['id']??0);
 }
 // Admin, Manager QAC, Team Leader QA: see all trials
 $countApproval=db()->prepare('SELECT COUNT(*) FROM trials_header h WHERE '.implode(' AND ',$approvalWhere));
 $countApproval->execute($approvalParams);
 $total=(int)$countApproval->fetchColumn();
 $pagination=pagination_state($total);
 $s=db()->prepare('SELECT h.*, u_approver.name AS approver_name, u_approver.email AS approver_email FROM trials_header h LEFT JOIN users u_approver ON u_approver.id=h.approver_user_id WHERE '.implode(' AND ',$approvalWhere).' ORDER BY h.updated_at DESC LIMIT ? OFFSET ?');
 foreach($approvalParams as $i=>$param) $s->bindValue($i+1,$param,PDO::PARAM_INT);
 $s->bindValue(count($approvalParams)+1,$pagination['per_page'],PDO::PARAM_INT);
 $s->bindValue(count($approvalParams)+2,$pagination['offset'],PDO::PARAM_INT);
 $s->execute();
 $items=$s->fetchAll();
 view('approvals',compact('items','pagination'));
 exit;
}

if(preg_match('#^/trials/(\d+)/approval$#',$path,$m)&&$method==='POST'){
 if(!can_approve_trials()) die('Forbidden');
 $t=trial($m[1]);
 if($t['progress_status']!=='Ready for Approval'){
  flash('Trial belum siap approval.');
  redirect('/approvals');
 }
 if(!is_admin()&&!is_manager_qac()&&!empty($t['approver_user_id'])&&(int)$t['approver_user_id']!==(int)(u()['id']??0)){
  flash('Trial ini ditugaskan ke approver lain.');
  redirect('/approvals');
 }
 if(!is_admin()&&!is_manager_qac()&&empty($t['approver_user_id'])){
  flash('Trial ini belum ditugaskan kepada Anda.');
  redirect('/approvals');
 }
 $decision=$_POST['decision']??'';
 if(!in_array($decision,['Approved','Need Revision','Rejected'],true)) die('Decision invalid');
 $comment=trim($_POST['approval_comment']??'');
 $password=$_POST['signature_password']??'';
 if($comment===''||$password===''){
  flash('Comment dan password e-signature wajib diisi.');
  redirect('/approvals');
 }
 $s=db()->prepare('SELECT password_hash FROM users WHERE id=? AND is_active=1');
 $s->execute([u()['id']]);
 $hash=$s->fetchColumn();
 if(!$hash||!password_verify($password,$hash)){
  flash('Password e-signature salah.');
  redirect('/approvals');
 }
 $managerName=user_display_name();
 if($decision==='Approved'){
  db()->prepare('UPDATE trials_header SET progress_status="Approved",final_decision="Approved",pending_with="",approved_by=?,approved_at=NOW(),approval_comment=?,updated_at=NOW() WHERE id=?')
   ->execute([$managerName,$comment,$t['id']]);
  $creatorId=user_id_by_email($t['created_by']??'');
  if($creatorId){
   createNotification([
    'user_id'=>$creatorId,
    'role_target'=>'Staff',
    'trial_id'=>$t['id'],
    'title'=>'Trial Approved',
    'message'=>'Trial '.$t['trial_code'].' - '.$t['product_name'].' sudah approved oleh Manager QAC.',
    'type'=>'approved',
   ]);
  }
  createNotification([
   'role_target'=>'Admin',
   'trial_id'=>$t['id'],
   'title'=>'Trial Approved',
   'message'=>'Trial '.$t['trial_code'].' - '.$t['product_name'].' sudah approved oleh Manager QAC.',
   'type'=>'approved',
  ]);
 }elseif($decision==='Need Revision'){
  db()->prepare('UPDATE trials_header SET progress_status="Need Revision",current_step="Revision",final_decision="Need Revision",pending_with="Staff",revision_no=revision_no+1,rejected_by=?,rejected_at=NOW(),approval_comment=?,updated_at=NOW() WHERE id=?')
   ->execute([$managerName,$comment,$t['id']]);
  $creatorId=user_id_by_email($t['created_by']??'');
  if($creatorId){
   createNotification([
    'user_id'=>$creatorId,
    'role_target'=>'Staff',
    'trial_id'=>$t['id'],
     'title'=>'Trial Need Revision',
     'message'=>'Trial '.$t['trial_code'].' membutuhkan revisi.',
     'type'=>'revision',
    ]);
  }
  createNotification([
   'role_target'=>'Admin',
   'trial_id'=>$t['id'],
   'title'=>'Trial Need Revision',
   'message'=>'Trial '.$t['trial_code'].' - '.$t['product_name'].' dikembalikan ke Staff untuk revisi.',
   'type'=>'revision',
  ]);
 }else{
  db()->prepare('UPDATE trials_header SET progress_status="Rejected",current_step="Closed",final_decision="Rejected",pending_with="",rejected_by=?,rejected_at=NOW(),approval_comment=?,updated_at=NOW() WHERE id=?')
   ->execute([$managerName,$comment,$t['id']]);
  $creatorId=user_id_by_email($t['created_by']??'');
  if($creatorId){
   createNotification([
    'user_id'=>$creatorId,
    'role_target'=>'Staff',
    'trial_id'=>$t['id'],
    'title'=>'Trial Rejected',
    'message'=>'Trial '.$t['trial_code'].' - '.$t['product_name'].' ditolak final oleh Manager QAC.',
    'type'=>'rejected',
   ]);
  }
  createNotification([
   'role_target'=>'Admin',
   'trial_id'=>$t['id'],
   'title'=>'Trial Rejected',
   'message'=>'Trial '.$t['trial_code'].' - '.$t['product_name'].' ditolak final oleh Manager QAC.',
   'type'=>'rejected',
  ]);
 }
 audit_log($t['id'],'manager_approval',[],['decision'=>$decision,'comment'=>$comment,'manager_name'=>$managerName,'manager_email'=>u()['email']]);
 logActivity($decision==='Approved'?'APPROVE':($decision==='Need Revision'?'NEED_REVISION':'REJECT'),'APPROVAL',$t['id'],$t['trial_code'],$t,['decision'=>$decision,'comment'=>$comment,'manager_name'=>$managerName]);
 redirect('/approvals');
}

if($path==='/admin/users'||$path==='/settings/users'){
 if(!is_admin()) die('Admin only');
 $filters=['q'=>trim($_GET['q']??'')];
 $where=['deleted_at IS NULL'];
 $params=[];
 if($filters['q']!==''){
  $where[]='(name LIKE ? OR email LIKE ? OR role LIKE ? OR department LIKE ?)';
  $like='%'.$filters['q'].'%';
  array_push($params,$like,$like,$like,$like);
 }
 $countStmt=db()->prepare('SELECT COUNT(*) FROM users WHERE '.implode(' AND ',$where));
 $countStmt->execute($params);
 $pagination=pagination_state((int)$countStmt->fetchColumn());
 $s=db()->prepare('SELECT * FROM users WHERE '.implode(' AND ',$where).' ORDER BY role,name LIMIT ? OFFSET ?');
 foreach($params as $i=>$param) $s->bindValue($i+1,$param);
 $s->bindValue(count($params)+1,$pagination['per_page'],PDO::PARAM_INT);
 $s->bindValue(count($params)+2,$pagination['offset'],PDO::PARAM_INT);
 $s->execute();
 $users=$s->fetchAll();
 $roleCategories=role_categories();
 $hasSuperAdmin=(int)db()->query("SELECT COUNT(*) FROM users WHERE role='Super Admin' AND is_active=1 AND deleted_at IS NULL")->fetchColumn()>0;
 if(!is_super_admin()&&$hasSuperAdmin) $roleCategories=array_values(array_filter($roleCategories,fn($role)=>$role!=='Super Admin'));
 $customRoleCategories=opts('role_category');
 view('admin_users',compact('users','pagination','roleCategories','customRoleCategories','filters'));
 exit;
}

if($path==='/admin/users/role-categories/save'&&$method==='POST'){
 if(!is_super_admin()) die('Super Admin only');
 $name=trim($_POST['name']??'');
 $sort=(int)($_POST['sort_order']??0);
 if($name===''){
  flash('Nama kategori role wajib diisi.');
  redirect('/settings/users');
 }
 if(strlen($name)>50){
  flash('Nama kategori hak akses maksimal 50 karakter.');
  redirect('/settings/users');
 }
 try{
  db()->prepare('INSERT INTO master_options(type,name,sort_order,is_active) VALUES("role_category",?,?,1) ON DUPLICATE KEY UPDATE sort_order=VALUES(sort_order),is_active=1,deleted_at=NULL,deleted_by=NULL')
   ->execute([$name,$sort]);
 }catch(PDOException $e){
  flash('Kategori role tersebut sudah ada.');
  redirect('/settings/users');
 }
 audit_log(null,'master_option_saved',[],['type'=>'role_category','name'=>$name,'sort_order'=>$sort]);
 logActivity('CREATE','MASTER',db()->lastInsertId(),'role_category - '.$name,null,['type'=>'role_category','name'=>$name,'sort_order'=>$sort]);
 redirect('/settings/users');
}

if(preg_match('#^/admin/users/role-categories/(\d+)/delete$#',$path,$m)&&$method==='POST'){
 if(!is_super_admin()) die('Super Admin only');
 $id=(int)$m[1];
 $s=db()->prepare('SELECT * FROM master_options WHERE id=? AND type="role_category"');
 $s->execute([$id]);
 $old=$s->fetch();
 if($old){
  db()->prepare('UPDATE master_options SET is_active=0,deleted_at=NOW(),deleted_by=? WHERE id=?')->execute([u()['id'],$id]);
  audit_log(null,'master_option_soft_deleted',[],['id'=>$id]);
  logActivity('SOFT_DELETE','MASTER',$id,'role_category - '.($old['name']??''),$old,null);
 }
 redirect('/settings/users');
}

if($path==='/admin/users/save'&&$method==='POST'){
 if(!is_admin()) die('Admin only');
 $role=trim($_POST['role']??'');
 $department=normalize_department($_POST['department']??'');
 $hasSuperAdmin=(int)db()->query("SELECT COUNT(*) FROM users WHERE role='Super Admin' AND is_active=1 AND deleted_at IS NULL")->fetchColumn()>0;
 if($role==='Super Admin'&&!is_super_admin()&&$hasSuperAdmin){
  flash('Hanya Super Admin yang bisa membuat atau memberikan role Super Admin.');
  redirect('/settings/users');
 }
 if(in_array(normalize_department($role),reviewer_department_codes(),true)){
  $role=normalize_department($role);
  $department=$role;
 }elseif($department===''){
  $department=normalize_department($role);
 }
 $hash=password_hash($_POST['password'],PASSWORD_DEFAULT);
 $old=null;
 $check=db()->prepare('SELECT * FROM users WHERE email=?');
 $check->execute([$_POST['email']]);
 $old=$check->fetch();
 if($old&&($old['role']??'')==='Super Admin'&&!is_super_admin()){
  flash('Hanya Super Admin yang bisa mengubah akun Super Admin.');
  redirect('/settings/users');
 }
 db()->prepare('INSERT INTO users(name,email,password_hash,role,department,is_active) VALUES(?,?,?,?,?,1) ON DUPLICATE KEY UPDATE name=VALUES(name),role=VALUES(role),department=VALUES(department),password_hash=VALUES(password_hash),is_active=1,deleted_at=NULL,deleted_by=NULL')
  ->execute([$_POST['name'],$_POST['email'],$hash,$role,$department]);
 audit_log(null,'admin_user_saved',[],['email'=>$_POST['email'],'role'=>$role,'department'=>$department]);
 logActivity($old?'UPDATE':'CREATE','USER',$old['id']??null,$_POST['email'],$old,['email'=>$_POST['email'],'role'=>$role,'department'=>$department]);
 redirect('/admin/users');
}

if(preg_match('#^/admin/users/(\d+)/delete$#',$path,$m)&&$method==='POST'){
 if(!is_admin()) die('403 Forbidden');
 $id=(int)$m[1];
 $s=db()->prepare('SELECT * FROM users WHERE id=?');
 $s->execute([$id]);
 $old=$s->fetch();
 if($old){
  if(($old['role']??'')==='Super Admin'&&!is_super_admin()){
   flash('Hanya Super Admin yang bisa menghapus akun Super Admin.');
   redirect('/settings/users');
  }
  db()->prepare('UPDATE users SET is_active=0,deleted_at=NOW(),deleted_by=? WHERE id=?')->execute([u()['id'],$id]);
  logActivity('SOFT_DELETE','USER',$id,$old['email']??'',$old,null);
 }
 redirect('/settings/users');
}

if($path==='/admin/access-rights'){
 if(!is_super_admin()) die('Super Admin only');
 $filters=['q'=>trim($_GET['q']??'')];
 $where=['deleted_at IS NULL'];
 $params=[];
 if($filters['q']!==''){
  $where[]='(name LIKE ? OR email LIKE ? OR role LIKE ? OR department LIKE ?)';
  $like='%'.$filters['q'].'%';
  array_push($params,$like,$like,$like,$like);
 }
 $countStmt=db()->prepare('SELECT COUNT(*) FROM users WHERE '.implode(' AND ',$where));
 $countStmt->execute($params);
 $pagination=pagination_state((int)$countStmt->fetchColumn());
 $s=db()->prepare('SELECT * FROM users WHERE '.implode(' AND ',$where).' ORDER BY role,name LIMIT ? OFFSET ?');
 foreach($params as $i=>$param) $s->bindValue($i+1,$param);
 $s->bindValue(count($params)+1,$pagination['per_page'],PDO::PARAM_INT);
 $s->bindValue(count($params)+2,$pagination['offset'],PDO::PARAM_INT);
 $s->execute();
 $users=$s->fetchAll();
 $roleCategories=role_categories();
 $customRoleCategories=opts('role_category');
 $reviewerDepartments=reviewer_department_codes();
 $customReviewerDepartments=opts('reviewer_department');
 $draftTrials=db()->query('SELECT id,trial_code,product_name,created_by,created_at FROM trials_header WHERE progress_status="Draft" AND deleted_at IS NULL ORDER BY updated_at DESC,id DESC LIMIT 100')->fetchAll();
 $staffUsers=db()->query('SELECT id,name,email FROM users WHERE role="Staff" AND is_active=1 AND deleted_at IS NULL ORDER BY name,email')->fetchAll();
 $draftPermissions=[];
 $permissionTableReady=true;
 try{
  $draftPermissions=db()->query('SELECT tep.*,h.trial_code,h.product_name,h.created_by AS owner_email,u.name AS user_name,u.email AS user_email,g.name AS granted_by_name,g.email AS granted_by_email
   FROM trial_edit_permissions tep
   JOIN trials_header h ON h.id=tep.trial_id
   JOIN users u ON u.id=tep.user_id
   LEFT JOIN users g ON g.id=tep.granted_by
   WHERE tep.can_edit=1 AND tep.revoked_at IS NULL AND h.deleted_at IS NULL
   ORDER BY tep.granted_at DESC,tep.id DESC')->fetchAll();
 }catch(Exception $e){
  $permissionTableReady=false;
 }
 view('admin_access_rights',compact('users','pagination','filters','roleCategories','customRoleCategories','reviewerDepartments','customReviewerDepartments','draftTrials','staffUsers','draftPermissions','permissionTableReady'));
 exit;
}

if($path==='/admin/access-rights/draft-permissions/grant'&&$method==='POST'){
 if(!is_super_admin()) die('Super Admin only');
 $trialId=(int)($_POST['trial_id']??0);
 $userId=(int)($_POST['user_id']??0);
 $trialStmt=db()->prepare('SELECT * FROM trials_header WHERE id=? AND progress_status="Draft" AND deleted_at IS NULL');
 $trialStmt->execute([$trialId]);
 $trialRow=$trialStmt->fetch();
 $userStmt=db()->prepare('SELECT * FROM users WHERE id=? AND role="Staff" AND is_active=1 AND deleted_at IS NULL');
 $userStmt->execute([$userId]);
 $targetUser=$userStmt->fetch();
 if(!$trialRow||!$targetUser){
  flash('Draft report atau user Staff tidak valid.');
  redirect('/admin/access-rights');
 }
 if(strcasecmp((string)$trialRow['created_by'],(string)$targetUser['email'])===0){
  flash('Owner sudah memiliki akses edit Draft report tersebut.');
  redirect('/admin/access-rights');
 }
 try{
  db()->prepare('INSERT INTO trial_edit_permissions(trial_id,user_id,can_edit,granted_by,granted_at) VALUES(?,?,1,?,NOW()) ON DUPLICATE KEY UPDATE can_edit=1,granted_by=VALUES(granted_by),granted_at=NOW(),revoked_by=NULL,revoked_at=NULL')
   ->execute([$trialId,$userId,u()['id']??null]);
 }catch(Exception $e){
  flash('Tabel permission belum tersedia. Jalankan migration database/20260702_trial_edit_permissions.sql.');
  redirect('/admin/access-rights');
 }
 audit_log($trialId,'trial_edit_permission_granted',[],[
  'trial_code'=>$trialRow['trial_code'],
  'owner'=>$trialRow['created_by'],
  'granted_to'=>$targetUser['email'],
  'granted_by'=>u()['email']??null,
 ]);
 logActivity('GRANT_PERMISSION','TRIAL',$trialId,$trialRow['trial_code'],null,['granted_to'=>$targetUser['email'],'permission'=>'edit_draft']);
 flash('Izin edit Draft report berhasil diberikan.');
 redirect('/admin/access-rights');
}

if(preg_match('#^/admin/access-rights/draft-permissions/(\d+)/revoke$#',$path,$m)&&$method==='POST'){
 if(!is_super_admin()) die('Super Admin only');
 $id=(int)$m[1];
 try{
  $s=db()->prepare('SELECT tep.*,h.trial_code,h.created_by,u.email AS user_email FROM trial_edit_permissions tep JOIN trials_header h ON h.id=tep.trial_id JOIN users u ON u.id=tep.user_id WHERE tep.id=? AND tep.revoked_at IS NULL');
  $s->execute([$id]);
  $old=$s->fetch();
  if($old){
   db()->prepare('UPDATE trial_edit_permissions SET can_edit=0,revoked_by=?,revoked_at=NOW() WHERE id=?')->execute([u()['id']??null,$id]);
   audit_log((int)$old['trial_id'],'trial_edit_permission_revoked',$old,[
    'trial_code'=>$old['trial_code'],
    'owner'=>$old['created_by'],
    'revoked_from'=>$old['user_email'],
    'revoked_by'=>u()['email']??null,
   ]);
   logActivity('REVOKE_PERMISSION','TRIAL',$old['trial_id'],$old['trial_code'],$old,['revoked_from'=>$old['user_email'],'permission'=>'edit_draft']);
   flash('Izin edit Draft report berhasil dicabut.');
  }
 }catch(Exception $e){
  flash('Tabel permission belum tersedia. Jalankan migration database/20260702_trial_edit_permissions.sql.');
 }
 redirect('/admin/access-rights');
}

if($path==='/admin/access-rights/reviewer-departments/save'&&$method==='POST'){
 if(!is_super_admin()) die('Super Admin only');
 $name=normalize_department($_POST['name']??'');
 $sort=(int)($_POST['sort_order']??0);
 if($name===''){
  flash('Nama kategori reviewer wajib diisi.');
  redirect('/admin/access-rights');
 }
 if(strlen($name)>50){
  flash('Nama kategori reviewer maksimal 50 karakter.');
  redirect('/admin/access-rights');
 }
 try{
  db()->prepare('INSERT INTO master_options(type,name,sort_order,is_active) VALUES("reviewer_department",?,?,1) ON DUPLICATE KEY UPDATE sort_order=VALUES(sort_order),is_active=1,deleted_at=NULL,deleted_by=NULL')
   ->execute([$name,$sort]);
 }catch(PDOException $e){
  flash('Kategori reviewer tersebut sudah ada.');
  redirect('/admin/access-rights');
 }
 audit_log(null,'master_option_saved',[],['type'=>'reviewer_department','name'=>$name,'sort_order'=>$sort]);
 logActivity('CREATE','MASTER',db()->lastInsertId(),'reviewer_department - '.$name,null,['type'=>'reviewer_department','name'=>$name,'sort_order'=>$sort]);
 redirect('/admin/access-rights');
}

if(preg_match('#^/admin/access-rights/reviewer-departments/(\d+)/delete$#',$path,$m)&&$method==='POST'){
 if(!is_super_admin()) die('Super Admin only');
 $id=(int)$m[1];
 $s=db()->prepare('SELECT * FROM master_options WHERE id=? AND type="reviewer_department"');
 $s->execute([$id]);
 $old=$s->fetch();
 if($old){
  db()->prepare('UPDATE master_options SET is_active=0,deleted_at=NOW(),deleted_by=? WHERE id=?')->execute([u()['id'],$id]);
  audit_log(null,'master_option_soft_deleted',[],['id'=>$id]);
  logActivity('SOFT_DELETE','MASTER',$id,'reviewer_department - '.($old['name']??''),$old,null);
 }
 redirect('/admin/access-rights');
}

if(preg_match('#^/admin/access-rights/users/(\d+)/role$#',$path,$m)&&$method==='POST'){
 if(!is_super_admin()) die('Super Admin only');
 $id=(int)$m[1];
 if($id===(int)(u()['id']??0)){
  flash('Tidak bisa mengubah hak akses akun sendiri dari halaman ini.');
  redirect('/admin/access-rights');
 }
 $role=trim($_POST['role']??'');
 $department=normalize_department($_POST['department']??'');
 if($role===''||!in_array($role,role_categories(),true)){
  flash('Kategori hak akses tidak valid.');
  redirect('/admin/access-rights');
 }
 if(in_array(normalize_department($role),reviewer_department_codes(),true)){
  $role=normalize_department($role);
  $department=$role;
 }elseif($department===''){
  $department=normalize_department($role);
 }
 $s=db()->prepare('SELECT * FROM users WHERE id=? AND deleted_at IS NULL');
 $s->execute([$id]);
 $old=$s->fetch();
 if(!$old){
  flash('User tidak ditemukan.');
  redirect('/admin/access-rights');
 }
 db()->prepare('UPDATE users SET role=?,department=? WHERE id=?')->execute([$role,$department,$id]);
 audit_log(null,'admin_user_saved',$old,['id'=>$id,'email'=>$old['email'],'role'=>$role,'department'=>$department]);
 logActivity('UPDATE','USER',$id,$old['email']??'',$old,['role'=>$role,'department'=>$department]);
 flash('Hak akses user berhasil diperbarui.');
 redirect('/admin/access-rights');
}

if($path==='/admin/trash'){
 if(!is_admin()) die('Admin only');
 $filters=[
  'q'=>trim($_GET['q']??''),
  'deleted_by'=>trim($_GET['deleted_by']??''),
  'date_from'=>trim($_GET['date_from']??''),
  'date_to'=>trim($_GET['date_to']??''),
 ];
 $where=['h.deleted_at IS NOT NULL'];
 $params=[];
 if($filters['q']!==''){
  $where[]='(h.trial_code LIKE ? OR h.product_name LIKE ? OR h.product_type LIKE ?)';
  $like='%'.$filters['q'].'%';
  array_push($params,$like,$like,$like);
 }
 if($filters['deleted_by']!==''){
  $where[]='u_del.name LIKE ?';
  $params[]='%'.$filters['deleted_by'].'%';
 }
 if($filters['date_from']!==''&&preg_match('/^\d{4}-\d{2}-\d{2}$/',$filters['date_from'])){
  $where[]='h.deleted_at>=?';
  $params[]=$filters['date_from'].' 00:00:00';
 }
 if($filters['date_to']!==''&&preg_match('/^\d{4}-\d{2}-\d{2}$/',$filters['date_to'])){
  $where[]='h.deleted_at<=?';
  $params[]=$filters['date_to'].' 23:59:59';
 }
 $countSql='SELECT COUNT(*) FROM trials_header h LEFT JOIN users u_del ON u_del.id=h.deleted_by WHERE '.implode(' AND ',$where);
 $cs=db()->prepare($countSql);
 $cs->execute($params);
 $pagination=pagination_state((int)$cs->fetchColumn());
 $sql='SELECT h.*,u_del.name AS deleted_by_name,u_del.email AS deleted_by_email,h.deleted_at,
  u_creator.name AS creator_name
  FROM trials_header h
  LEFT JOIN users u_del ON u_del.id=h.deleted_by
  LEFT JOIN users u_creator ON u_creator.email=h.created_by
  WHERE '.implode(' AND ',$where).'
  ORDER BY h.deleted_at DESC,h.id DESC
  LIMIT '.(int)$pagination['per_page'].' OFFSET '.(int)$pagination['offset'];
 $s=db()->prepare($sql);
 $s->execute($params);
 $items=$s->fetchAll();
 view('admin_trash',compact('items','filters','pagination'));
 exit;
}

if(preg_match('#^/admin/trash/restore/(\d+)$#',$path,$m)&&$method==='POST'){
 if(!is_admin()) die('Admin only');
 $id=(int)$m[1];
 $t=trial($id);
 if(!$t['deleted_at']){
  flash('Trial ini tidak dalam status terhapus.');
  redirect('/admin/trash');
 }
 db()->prepare('UPDATE trials_header SET deleted_at=NULL,deleted_by=NULL,updated_at=NOW() WHERE id=?')->execute([$id]);
 logActivity('RESTORE','TRIAL',$id,$t['trial_code'],$t,['action'=>'restored from trash']);
 audit_log($id,'trial_restored',$t,['action'=>'restored from trash','restored_by'=>u()['email']]);
 createNotification([
  'role_target'=>'Admin',
  'trial_id'=>$id,
  'title'=>'Trial Restored',
  'message'=>'Trial '.$t['trial_code'].' - '.$t['product_name'].' berhasil direstore dari trash.',
  'type'=>'info',
 ]);
 flash('Trial berhasil direstore.');
 redirect('/admin/trash');
}

if(preg_match('#^/admin/trash/delete/(\d+)$#',$path,$m)&&$method==='POST'){
 if(!is_admin()) die('Admin only');
 $id=(int)$m[1];
 $t=trial($id);
 if(!$t['deleted_at']){
  flash('Trial ini tidak dalam status terhapus.');
  redirect('/admin/trash');
 }
 $confirm_token=trim($_POST['confirm_token']??'');
 if($confirm_token!=='PERMANENT'){
  flash('Konfirmasi tidak valid. Ketik PERMANENT untuk menghapus permanen.');
  redirect('/admin/trash');
 }
 db()->beginTransaction();
 try{
  db()->prepare('DELETE FROM trial_edit_permissions WHERE trial_id=?')->execute([$id]);
  db()->prepare('DELETE FROM trials_results WHERE trial_id=?')->execute([$id]);
  db()->prepare('DELETE FROM trials_weighing WHERE trial_id=?')->execute([$id]);
  db()->prepare('DELETE FROM trials_review WHERE trial_id=?')->execute([$id]);
  db()->prepare('DELETE FROM trial_attachment_files WHERE trial_id=?')->execute([$id]);
  db()->prepare('DELETE FROM notifications WHERE trial_id=?')->execute([$id]);
  db()->prepare('DELETE FROM audit_logs WHERE trial_id=?')->execute([$id]);
  db()->prepare('DELETE FROM trials_header WHERE id=?')->execute([$id]);
  db()->commit();
 }catch(Exception $e){
  db()->rollBack();
  flash('Gagal menghapus permanen: '.$e->getMessage());
  redirect('/admin/trash');
 }
 logActivity('PERMANENT_DELETE','TRIAL',$id,$t['trial_code'],$t,null);
 flash('Trial berhasil dihapus permanen.');
 redirect('/admin/trash');
}

if($path==='/admin/products'||$path==='/templates/products'){
 if(!can_manage_templates()) die('Admin/Staff only');
 $pagination=pagination_state((int)db()->query('SELECT COUNT(*) FROM products WHERE is_active=1 AND deleted_at IS NULL')->fetchColumn());
 $s=db()->prepare('SELECT * FROM products WHERE is_active=1 AND deleted_at IS NULL ORDER BY product_name LIMIT ? OFFSET ?');
 $s->bindValue(1,$pagination['per_page'],PDO::PARAM_INT);
 $s->bindValue(2,$pagination['offset'],PDO::PARAM_INT);
 $s->execute();
 $products=$s->fetchAll();
 $editProduct=null;
 if(isset($_GET['edit'])&&ctype_digit((string)$_GET['edit'])){
  $s=db()->prepare('SELECT * FROM products WHERE id=? AND is_active=1');
  $s->execute([(int)$_GET['edit']]);
  $editProduct=$s->fetch();
 }
 view('admin_products',compact('products','editProduct','pagination'));
 exit;
}

if(in_array($path,['/admin/products/save','/templates/products/save'],true)&&$method==='POST'){
 if(!can_manage_templates()) die('Admin/Staff only');
 $id=(int)($_POST['id']??0);
 $productName=trim($_POST['product_name']??'');
 $fgCode=trim($_POST['finish_good_code']??'');
 if($productName===''||$fgCode===''){
  flash('Product Name dan Finish Good Code wajib diisi.');
  redirect('/templates/products');
 }
 try{
 if($id>0){
   $oldStmt=db()->prepare('SELECT * FROM products WHERE id=?');
   $oldStmt->execute([$id]);
   $oldProduct=$oldStmt->fetch();
   db()->prepare('UPDATE products SET product_name=?,finish_good_code=?,is_active=1 WHERE id=?')
    ->execute([$productName,$fgCode,$id]);
  }else{
   $oldProduct=null;
   db()->prepare('INSERT INTO products(product_name,finish_good_code,is_active) VALUES(?,?,1) ON DUPLICATE KEY UPDATE finish_good_code=VALUES(finish_good_code),is_active=1')
    ->execute([$productName,$fgCode]);
  }
 }catch(PDOException $e){
  flash('Product name sudah digunakan item lain.');
  redirect('/templates/products');
 }
 audit_log(null,'product_saved',[],['id'=>$id,'product_name'=>$productName,'finish_good_code'=>$fgCode]);
 logActivity($id>0?'UPDATE':'CREATE','PRODUCT',$id?:db()->lastInsertId(),$productName,$oldProduct??null,['product_name'=>$productName,'finish_good_code'=>$fgCode]);
 redirect('/templates/products');
}

if(preg_match('#^/(admin|templates)/products/(\d+)/delete$#',$path,$m)&&$method==='POST'){
 if(!can_manage_templates()) die('Admin/Staff only');
 $id=(int)$m[2];
 $s=db()->prepare('SELECT * FROM products WHERE id=?');
 $s->execute([$id]);
 $old=$s->fetch();
 db()->prepare('UPDATE products SET is_active=0,deleted_at=NOW(),deleted_by=? WHERE id=?')->execute([u()['id'],$id]);
 audit_log(null,'product_soft_deleted',[],['id'=>$id]);
 logActivity('SOFT_DELETE','PRODUCT',$id,$old['product_name']??'',$old,null);
 redirect('/templates/products');
}

if($path==='/admin/masters'||$path==='/templates/master'){
 if(!can_manage_master()) die('Admin/Staff only');
 $masterWhere=['is_active=1','deleted_at IS NULL'];
 if(!is_super_admin()) $masterWhere[]='type NOT IN ("role_category","reviewer_department")';
 $pagination=pagination_state((int)db()->query('SELECT COUNT(*) FROM master_options WHERE '.implode(' AND ',$masterWhere))->fetchColumn());
 $s=db()->prepare('SELECT * FROM master_options WHERE '.implode(' AND ',$masterWhere).' ORDER BY type,sort_order,name LIMIT ? OFFSET ?');
 $s->bindValue(1,$pagination['per_page'],PDO::PARAM_INT);
 $s->bindValue(2,$pagination['offset'],PDO::PARAM_INT);
 $s->execute();
 $opts=$s->fetchAll();
 $editMaster=null;
 if(isset($_GET['edit'])&&ctype_digit((string)$_GET['edit'])){
  $s=db()->prepare('SELECT * FROM master_options WHERE id=? AND is_active=1');
  $s->execute([(int)$_GET['edit']]);
  $editMaster=$s->fetch();
  if($editMaster&&in_array($editMaster['type'],['role_category','reviewer_department'],true)&&!is_super_admin()) $editMaster=null;
 }
 view('admin_masters',compact('opts','editMaster','pagination'));
 exit;
}

if(in_array($path,['/admin/masters/save','/templates/master/save'],true)&&$method==='POST'){
 if(!can_manage_master()) die('Admin/Staff only');
 $id=(int)($_POST['id']??0);
 $type=trim($_POST['type']??'');
 $name=trim($_POST['name']??'');
 $sort=(int)($_POST['sort_order']??0);
 $allowedTypes=['validation_category','validation_scope','machine_used','product_type','attachment_category'];
 if(is_super_admin()) $allowedTypes=array_merge($allowedTypes,['role_category','reviewer_department']);
 if(!in_array($type,$allowedTypes,true)||$name===''){
  flash('Type atau name master tidak valid.');
  redirect('/templates/master');
 }
 if(in_array($type,['role_category','reviewer_department'],true)&&strlen($name)>50){
  flash('Nama role/reviewer maksimal 50 karakter.');
  redirect('/templates/master');
 }
 try{
 if($id>0){
   $oldStmt=db()->prepare('SELECT * FROM master_options WHERE id=?');
   $oldStmt->execute([$id]);
   $oldMaster=$oldStmt->fetch();
   db()->prepare('UPDATE master_options SET type=?,name=?,sort_order=?,is_active=1 WHERE id=?')
    ->execute([$type,$name,$sort,$id]);
  }else{
   $oldMaster=null;
   db()->prepare('INSERT INTO master_options(type,name,sort_order,is_active) VALUES(?,?,?,1) ON DUPLICATE KEY UPDATE sort_order=VALUES(sort_order),is_active=1')
    ->execute([$type,$name,$sort]);
  }
 }catch(PDOException $e){
  flash('Master option dengan type dan name tersebut sudah ada.');
  redirect('/templates/master');
 }
 audit_log(null,'master_option_saved',[],['id'=>$id,'type'=>$type,'name'=>$name,'sort_order'=>$sort]);
 logActivity($id>0?'UPDATE':'CREATE','MASTER',$id?:db()->lastInsertId(),$type.' - '.$name,$oldMaster??null,['type'=>$type,'name'=>$name,'sort_order'=>$sort]);
 redirect('/templates/master');
}

if(preg_match('#^/(admin|templates)/master/(\d+)/delete$#',$path,$m)&&$method==='POST'){
 if(!can_manage_master()) die('Admin/Staff only');
 $id=(int)$m[2];
 $s=db()->prepare('SELECT * FROM master_options WHERE id=?');
 $s->execute([$id]);
 $old=$s->fetch();
 if($old&&in_array($old['type'],['role_category','reviewer_department'],true)&&!is_super_admin()){
  flash('Hanya Super Admin yang bisa menghapus role/reviewer master.');
  redirect('/templates/master');
 }
 db()->prepare('UPDATE master_options SET is_active=0,deleted_at=NOW(),deleted_by=? WHERE id=?')->execute([u()['id'],$id]);
 audit_log(null,'master_option_soft_deleted',[],['id'=>$id]);
 logActivity('SOFT_DELETE','MASTER',$id,($old['type']??'').' - '.($old['name']??''),$old,null);
 redirect('/templates/master');
}

if($path==='/admin/parameters'||$path==='/templates/parameters'){
 if(!can_manage_parameters()) die('Admin/Staff only');
 $pagination=pagination_state((int)db()->query('SELECT COUNT(*) FROM validation_parameters WHERE is_active=1 AND deleted_at IS NULL')->fetchColumn());
 $s=db()->prepare('SELECT * FROM validation_parameters WHERE is_active=1 AND deleted_at IS NULL ORDER BY product_type,sort_order,id LIMIT ? OFFSET ?');
 $s->bindValue(1,$pagination['per_page'],PDO::PARAM_INT);
 $s->bindValue(2,$pagination['offset'],PDO::PARAM_INT);
 $s->execute();
 $params=$s->fetchAll();
 $editParameter=null;
 if(isset($_GET['edit'])&&ctype_digit((string)$_GET['edit'])){
  $s=db()->prepare('SELECT * FROM validation_parameters WHERE id=? AND is_active=1');
  $s->execute([(int)$_GET['edit']]);
  $editParameter=$s->fetch();
 }
 view('admin_parameters',compact('params','editParameter','pagination'));
 exit;
}

if(in_array($path,['/admin/parameters/save','/templates/parameters/save'],true)&&$method==='POST'){
 if(!can_manage_parameters()) die('Admin/Staff only');
 $id=(int)($_POST['id']??0);
 $productType=trim($_POST['product_type']??'');
 $parameterName=trim($_POST['parameter_name']??'');
 $specification=trim($_POST['specification']??'');
 $sort=(int)($_POST['sort_order']??0);
 if(!option_exists('product_type',$productType)){
  flash('Product type tidak valid.');
  redirect('/templates/parameters');
 }
 if($parameterName===''){
  flash('Parameter wajib diisi.');
  redirect('/templates/parameters');
 }
 if($id>0){
  $oldStmt=db()->prepare('SELECT * FROM validation_parameters WHERE id=?');
  $oldStmt->execute([$id]);
  $oldParam=$oldStmt->fetch();
  db()->prepare('UPDATE validation_parameters SET product_type=?,parameter_name=?,specification=?,sort_order=?,is_active=1 WHERE id=?')
   ->execute([$productType,$parameterName,$specification,$sort,$id]);
 }else{
  $oldParam=null;
  db()->prepare('INSERT INTO validation_parameters(product_type,parameter_name,specification,sort_order,is_active) VALUES(?,?,?,?,1)')
   ->execute([$productType,$parameterName,$specification,$sort]);
 }
 audit_log(null,'validation_parameter_saved',[],['id'=>$id,'product_type'=>$productType,'parameter_name'=>$parameterName]);
 logActivity($id>0?'UPDATE':'CREATE','PARAMETER',$id?:db()->lastInsertId(),$productType.' - '.$parameterName,$oldParam??null,['product_type'=>$productType,'parameter_name'=>$parameterName,'specification'=>$specification,'sort_order'=>$sort]);
 redirect('/templates/parameters');
}

if(preg_match('#^/(admin|templates)/parameters/(\d+)/delete$#',$path,$m)&&$method==='POST'){
 if(!can_manage_parameters()) die('Admin/Staff only');
 $id=(int)$m[2];
 $s=db()->prepare('SELECT * FROM validation_parameters WHERE id=?');
 $s->execute([$id]);
 $old=$s->fetch();
 db()->prepare('UPDATE validation_parameters SET is_active=0,deleted_at=NOW(),deleted_by=? WHERE id=?')->execute([u()['id'],$id]);
 audit_log(null,'validation_parameter_soft_deleted',[],['id'=>$id]);
 logActivity('SOFT_DELETE','PARAMETER',$id,($old['product_type']??'').' - '.($old['parameter_name']??''),$old,null);
 redirect('/templates/parameters');
}

http_response_code(404);
echo 'Not Found';
