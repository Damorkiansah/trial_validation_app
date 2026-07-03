<?php
$sessionDir=__DIR__.'/../storage/sessions';
if(!is_dir($sessionDir)) @mkdir($sessionDir,0775,true);
if(is_dir($sessionDir)&&is_writable($sessionDir)) session_save_path($sessionDir);
session_start();
$config=require __DIR__.'/../config/database.php';
try{
 $pdo=new PDO("mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}",$config['user'],$config['pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
}catch(Exception $e){die('Database error: '.$e->getMessage().'<br>Import database/trial_validation_system_mysql_v2.sql dulu.');}
if(!function_exists('str_contains')){
 function str_contains($haystack,$needle){
  $haystack=(string)$haystack;
  $needle=(string)$needle;
  return $needle===''||strpos($haystack,$needle)!==false;
 }
}
if(!function_exists('str_starts_with')){
 function str_starts_with($haystack,$needle){
  $haystack=(string)$haystack;
  $needle=(string)$needle;
  return $needle===''||strncmp($haystack,$needle,strlen($needle))===0;
 }
}
if(!function_exists('str_ends_with')){
 function str_ends_with($haystack,$needle){
  $haystack=(string)$haystack;
  $needle=(string)$needle;
  if($needle==='') return true;
  $len=strlen($needle);
  return $len<=strlen($haystack)&&substr($haystack,-$len)===$needle;
 }
}
function db(){global $pdo;return $pdo;}
function u(){return $_SESSION['user']??null;}
function role(){return u()['role']??'guest';}
function user_display_name($user=null){
 $user=$user?:u();
 if(!$user) return '';
 $name=trim((string)($user['name']??''));
 return $name!==''?$name:($user['email']??'');
}
function display_person_name($value){
 $value=trim((string)$value);
 if($value==='') return '';
 if(str_contains($value,'@')){
  try{
   $s=db()->prepare('SELECT name FROM users WHERE email=? LIMIT 1');
   $s->execute([$value]);
   $name=trim((string)$s->fetchColumn());
   if($name!=='') return $name;
  }catch(Exception $e){}
 }
 return $value;
}
function user_id_by_email($email){
 $email=trim((string)$email);
 if($email==='') return null;
 $s=db()->prepare('SELECT id FROM users WHERE email=? AND is_active=1 LIMIT 1');
 $s->execute([$email]);
 $id=$s->fetchColumn();
 return $id?(int)$id:null;
}
function is_super_admin(){return role()==='Super Admin';}
function is_admin(){return role()==='Admin'||is_super_admin();}
function is_staff(){return role()==='Staff'||is_admin();}
function is_manager_qac(){return role()==='Manager QAC';}
function is_viewer(){return role()==='Viewer';}
function reviewer_department_codes(){
 $defaults=['PRD','RNI','QAC','PRNI','PI'];
 try{
  $codes=$defaults;
  foreach(opts('reviewer_department') as $option){
   $code=normalize_department($option['name']??'');
   if($code!==''&&!in_array($code,$codes,true)) $codes[]=$code;
  }
  return $codes;
 }catch(Exception $e){
  return $defaults;
 }
}
function normalize_department($dept){
 $dept=strtoupper(trim((string)$dept));
 return preg_replace('/\s+/',' ',$dept);
}
function user_department(){
 $dept=normalize_department(u()['department']??'');
 return $dept!==''?$dept:normalize_department(role());
}
function review_departments_for_user(){
 $codes=reviewer_department_codes();
 $items=[];
 $role=normalize_department(role());
 $dept=user_department();
 if(in_array($role,$codes,true)) $items[]=$role;
 if(in_array($dept,$codes,true)) $items[]=$dept;
 return array_values(array_unique($items));
}
function is_reviewer(){return !is_manager_qac()&&count(review_departments_for_user())>0;}
function role_label(){
 if(is_super_admin()) return 'Super Admin';
 if(is_admin()) return 'Admin';
 if(role()==='Staff') return 'Staff Trial';
 if(is_viewer()) return 'Viewer';
 if(is_reviewer()) return 'Reviewer';
 if(is_manager_qac()) return 'Manager QAC';
 return role();
}
function user_initials(){
 $name=trim((string)(u()['name']??u()['email']??'U'));
 $parts=preg_split('/\s+/', $name);
 $first=$parts[0][0]??'U';
 $second=count($parts)>1?($parts[1][0]??''):'';
 return strtoupper($first.$second);
}
function app_public_base_path(){
 $script=str_replace('\\','/',$_SERVER['SCRIPT_NAME']??'');
 $base=rtrim(str_replace('\\','/',dirname($script)),'/');
 return $base==='/'?'':$base;
}
function app_base_path(){
 $publicBase=app_public_base_path();
 $requestPath=parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH);
 $requestPath='/'.ltrim((string)$requestPath,'/');
 if($publicBase!==''&&($requestPath===$publicBase||str_starts_with($requestPath,$publicBase.'/'))) return $publicBase;
 $base=$publicBase;
 if(substr($base,-7)==='/public') $base=substr($base,0,-7);
 return $base==='/'?'':$base;
}
function normalize_request_path($path){
 $path='/'.ltrim((string)$path,'/');
 foreach(array_unique([app_public_base_path(),app_base_path()]) as $base){
  if($base!==''&&($path===$base||str_starts_with($path,$base.'/'))){
   $path=substr($path,strlen($base));
   $path=$path===''?'/':$path;
   break;
  }
 }
 return $path;
}
function url($path='/'){
 $path=(string)$path;
 if($path===''||$path==='#') return $path;
 if(preg_match('#^[a-z][a-z0-9+.-]*:#i',$path)) return $path;
 $base=app_base_path();
 if($path[0]!=='/') $path='/'.$path;
 return ($base!==''?$base:'').$path;
}
function apply_base_path_to_html($html){
 $base=app_base_path();
 if($base==='') return $html;
 $replacements=[
  'href="/'=>'href="'.$base.'/',
  'action="/'=>'action="'.$base.'/',
  'src="/'=>'src="'.$base.'/',
  "fetch('/"=>"fetch('".$base."/",
  'fetch("/'=>'fetch("'.$base.'/',
 ];
 return strtr($html,$replacements);
}
function require_login(){if(!u()) redirect('/login');}
function redirect($p){header('Location: '.url($p));exit;}
function section_display_name($section){
 $names=['Packaging'=>'Empty packaging (gr)','Filling'=>'Filling Weight (gr)'];
 return $names[$section]??$section;
}
function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function flash($m=null){if($m!==null){$_SESSION['flash']=$m;return;} if(isset($_SESSION['flash'])){echo '<div class="flash">'.h($_SESSION['flash']).'</div>';unset($_SESSION['flash']);}}
function view($name,$data=[]){extract($data);ob_start();include __DIR__.'/views/layout.php';echo apply_base_path_to_html(ob_get_clean());}
function partial($name,$data=[]){extract($data);include __DIR__.'/views/'.$name.'.php';}
function logActivity($action,$module,$recordId=null,$recordLabel=null,$oldData=null,$newData=null){
 try{
  $s=db()->prepare('INSERT INTO activity_logs(user_id,user_name,user_role,action,module,record_id,record_label,old_data,new_data,ip_address,user_agent,created_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,NOW())');
  $s->execute([
   u()['id']??null,
   user_display_name(),
   role(),
   strtoupper((string)$action),
   strtoupper((string)$module),
   $recordId,
   $recordLabel,
   $oldData===null?null:json_encode($oldData,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT),
   $newData===null?null:json_encode($newData,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT),
   $_SERVER['REMOTE_ADDR']??null,
   substr($_SERVER['HTTP_USER_AGENT']??'',0,255),
  ]);
 }catch(Exception $e){}
}
function pagination_state($total,$perPage=10){
 $perPage=max(1,(int)$perPage);
 $total=max(0,(int)$total);
 $pages=max(1,(int)ceil($total/$perPage));
 $page=max(1,(int)($_GET['page']??1));
 if($page>$pages) $page=$pages;
 return ['page'=>$page,'per_page'=>$perPage,'total'=>$total,'pages'=>$pages,'offset'=>($page-1)*$perPage];
}
function render_pagination($pagination){
 if(empty($pagination)||($pagination['pages']??1)<=1) return;
 $page=(int)$pagination['page'];
 $pages=(int)$pagination['pages'];
 if($pages>10){
  echo '<nav class="pagination"><span class="page-info">Halaman '.$page.' dari '.$pages.'</span>';
  $params=$_GET;
  // Prev button
  $params['page']=max(1,$page-1);
  echo '<a class="'.($page<=1?'disabled':'').'" href="?'.h(http_build_query($params)).'">&laquo;</a>';
  // First page
  $params['page']=1;
  echo '<a class="'.($page===1?'active':'').'" href="?'.h(http_build_query($params)).'">1</a>';
  // Ellipsis after first
  if($page>3) echo '<span class="ellipsis">...</span>';
  // Middle pages
  $start=max(2,$page-1);
  $end=min($pages-1,$page+1);
  if($page<=3) $end=min($pages-1,4);
  if($page>=$pages-2) $start=max(2,$pages-3);
  for($i=$start;$i<=$end;$i++){
   $params['page']=$i;
   echo '<a class="'.($i===$page?'active':'').'" href="?'.h(http_build_query($params)).'">'.$i.'</a>';
  }
  // Ellipsis before last
  if($page<$pages-2) echo '<span class="ellipsis">...</span>';
  // Last page
  if($pages>1){
   $params['page']=$pages;
   echo '<a class="'.($page===$pages?'active':'').'" href="?'.h(http_build_query($params)).'">'.$pages.'</a>';
  }
  // Next button
  $params['page']=min($pages,$page+1);
  echo '<a class="'.($page>=$pages?'disabled':'').'" href="?'.h(http_build_query($params)).'">&raquo;</a>';
  echo '</nav>';
 }else{
  $params=$_GET;
  echo '<nav class="pagination">';
  $params['page']=max(1,$page-1);
  echo '<a class="'.($page<=1?'disabled':'').'" href="?'.h(http_build_query($params)).'">&laquo;</a>';
  for($i=1;$i<=$pages;$i++){
   $params['page']=$i;
   echo '<a class="'.($i===$page?'active':'').'" href="?'.h(http_build_query($params)).'">'.$i.'</a>';
  }
  $params['page']=min($pages,$page+1);
  echo '<a class="'.($page>=$pages?'disabled':'').'" href="?'.h(http_build_query($params)).'">&raquo;</a>';
  echo '</nav>';
 }
}
function trial($id){$s=db()->prepare('SELECT * FROM trials_header WHERE id=?');$s->execute([$id]);$r=$s->fetch();if(!$r) die('Trial tidak ditemukan');return $r;}
function opts($type){$s=db()->prepare('SELECT * FROM master_options WHERE type=? AND is_active=1 ORDER BY sort_order,name');$s->execute([$type]);return $s->fetchAll();}
function role_categories(){
 $defaults=['Staff','Viewer','Manager QAC','Admin','Super Admin'];
 foreach(reviewer_department_codes() as $dept){
  if(!in_array($dept,$defaults,true)) $defaults[]=$dept;
 }
 $roles=$defaults;
 foreach(opts('role_category') as $option){
  $name=trim((string)($option['name']??''));
  if($name!==''&&!in_array($name,$roles,true)) $roles[]=$name;
 }
 return $roles;
}
function params_for($product_type){$s=db()->prepare('SELECT * FROM validation_parameters WHERE product_type=? AND is_active=1 ORDER BY sort_order,id');$s->execute([$product_type]);return $s->fetchAll();}
function is_trial_owner($t){
 return trim((string)($t['created_by']??''))!==''&&strcasecmp(trim((string)$t['created_by']),trim((string)(u()['email']??'')))===0;
}
function has_trial_edit_permission($trialId,$userId=null){
 $userId=$userId??(u()['id']??0);
 if(!$trialId||!$userId) return false;
 try{
  $s=db()->prepare('SELECT COUNT(*) FROM trial_edit_permissions WHERE trial_id=? AND user_id=? AND can_edit=1 AND revoked_at IS NULL');
  $s->execute([(int)$trialId,(int)$userId]);
  return (int)$s->fetchColumn()>0;
 }catch(Exception $e){
  return false;
 }
}
function can_edit($t){
 if(!is_staff()||!in_array($t['progress_status'],['Draft','Need Revision'],true)) return false;
 if(($t['progress_status']??'')==='Draft') return is_trial_owner($t)||has_trial_edit_permission($t['id']??0);
 return true;
}
function can_manage_settings(){return is_admin();}
function can_manage_master(){return is_admin()||role()==='Staff';}
function can_manage_parameters(){return is_admin()||role()==='Staff';}
function can_view_products_template(){return is_admin()||role()==='Staff';}
function can_manage_templates(){return is_admin()||role()==='Staff';}
function has_assigned_approval($userId=null){
 $userId=$userId??(u()['id']??0);
 if(!$userId) return false;
 try{
  $s=db()->prepare('SELECT COUNT(*) FROM trials_header WHERE progress_status="Ready for Approval" AND deleted_at IS NULL AND approver_user_id=?');
  $s->execute([(int)$userId]);
  return (int)$s->fetchColumn()>0;
 }catch(Exception $e){
  return false;
 }
}
function can_approve_trials(){
 if(is_admin()) return true;
 if(is_manager_qac()) return true;
 // Team Leader, Part Leader, Team Leader QA can also approve
 $approverRoles = ['Team Leader', 'Part Leader', 'Team Leader QA'];
 if(in_array(role(), $approverRoles, true)) return true;
 return has_assigned_approval();
}
function can_approve_trial($t){
 if(is_admin()) return true;
 if(is_manager_qac()) return true;
 // Team Leader, Part Leader, Team Leader QA can also approve
 $approverRoles = ['Team Leader', 'Part Leader', 'Team Leader QA'];
 if(in_array(role(), $approverRoles, true)) return true;
 if(!empty($t['approver_user_id'])) return (int)$t['approver_user_id']===(int)(u()['id']??0);
 return false;
}
function base_url(){return '';}
function csrf_token(){
 if(empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32));
 return $_SESSION['csrf_token'];
}
function csrf_field(){echo '<input type="hidden" name="csrf_token" value="'.h(csrf_token()).'">';}
function verify_csrf(){
 $token=$_POST['csrf_token']??'';
 if(!is_string($token)||!hash_equals(csrf_token(),$token)){
  http_response_code(419);
  die('Sesi form tidak valid. Refresh halaman lalu coba lagi.');
 }
}
function option_exists($type,$name){
 $s=db()->prepare('SELECT COUNT(*) FROM master_options WHERE type=? AND name=? AND is_active=1');
 $s->execute([$type,$name]);
 return (int)$s->fetchColumn()>0;
}
function multi_values($value){
 if(is_array($value)) $items=$value;
 else{
  $value=trim((string)$value);
  if($value==='') return [];
  $decoded=json_decode($value,true);
  if(is_array($decoded)) $items=$decoded;
  else $items=str_contains($value,',')?explode(',',$value):[$value];
 }
 $clean=[];
 foreach($items as $item){
  $item=trim((string)$item);
  if($item!==''&&!in_array($item,$clean,true)) $clean[]=$item;
 }
 return $clean;
}
function encode_multi_value($value){
 return json_encode(multi_values($value),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
}
function display_multi_value($value){
 $items=multi_values($value);
 return $items?implode(', ',$items):'';
}
function options_exist($type,$names){
 foreach(multi_values($names) as $name){
  if(!option_exists($type,$name)) return false;
 }
 return true;
}
function format_weight_value($value,$decimals=2){
 if($value===''||$value===null||!is_numeric($value)) return '-';
 $text=number_format((float)$value,$decimals,'.','');
 return rtrim(rtrim($text,'0'),'.');
}
function weighing_stats($items){
 $nums=[];
 $values=[];
 foreach($items as $item){
  if((int)($item['is_skipped']??0)===1) continue;
  $value=$item['weight_value']??null;
  if($value!==''&&$value!==null&&is_numeric($value)){
   $nums[]=(float)$value;
   $values[]=$value;
  }
 }
 return [
  'values'=>$values,
  'count'=>count($nums),
  'min'=>$nums?min($nums):null,
  'max'=>$nums?max($nums):null,
  'avg'=>$nums?array_sum($nums)/count($nums):null,
 ];
}
function can_view_trial($t){
 if(($t['progress_status']??'')==='Draft') return is_super_admin()||is_trial_owner($t)||has_trial_edit_permission($t['id']??0);
 if(is_viewer()) return true;
 // Admin, Staff, Manager QAC, Team Leader, Part Leader, Team Leader QA can view
 $approverRoles = ['Manager QAC', 'Team Leader', 'Part Leader', 'Team Leader QA'];
 if(is_staff()||in_array(role(), $approverRoles, true)) return true;
 if(is_reviewer()){
  $departments=review_departments_for_user();
  $placeholders=implode(',',array_fill(0,count($departments),'?'));
  $round=current_review_round($t);
  $roundFilter=in_array($t['progress_status']??'', ['In Review','Ready for Approval'], true)?' AND review_round='.(int)$round:'';
  $s=db()->prepare("SELECT COUNT(*) FROM trials_review WHERE trial_id=? AND UPPER(TRIM(department)) IN ($placeholders)$roundFilter");
  $s->execute(array_merge([$t['id']],$departments));
  return (int)$s->fetchColumn()>0;
 }
 return false;
}
function require_trial_view($t){if(!can_view_trial($t)) die('Forbidden');}
function current_review_round($t){return (int)($t['revision_no']??0)+1;}
function audit_log($trial_id,$action,$old_data=[],$new_data=[]){
 try{
  $s=db()->prepare('INSERT INTO audit_logs(trial_id,user_id,user_email,action,old_data,new_data,ip_address,user_agent,created_at) VALUES(?,?,?,?,?,?,?,?,NOW())');
  $s->execute([
   $trial_id,
   u()['id']??null,
   u()['email']??null,
   $action,
   json_encode($old_data,JSON_UNESCAPED_SLASHES),
   json_encode($new_data,JSON_UNESCAPED_SLASHES),
   $_SERVER['REMOTE_ADDR']??null,
   substr($_SERVER['HTTP_USER_AGENT']??'',0,255),
  ]);
 }catch(Exception $e){
  // Audit table may not exist until the hardening migration is imported.
 }
 $map=[
  'trial_created'=>['CREATE','TRIAL'],
  'trial_header_updated'=>['UPDATE','TRIAL'],
  'trial_edit_permission_granted'=>['GRANT_PERMISSION','TRIAL'],
  'trial_edit_permission_revoked'=>['REVOKE_PERMISSION','TRIAL'],
  'validation_saved'=>['UPDATE','PARAMETER'],
  'weighing_saved'=>['UPDATE','TRIAL'],
  'attachments_uploaded'=>['CREATE','ATTACHMENT'],
  'attachment_deleted'=>['DELETE','ATTACHMENT'],
  'submitted_for_review'=>['SUBMIT_REVIEW','REVIEW'],
  'approval_assignee_selected'=>['SELECT_APPROVER','APPROVAL'],
  'department_reviewed'=>['UPDATE','REVIEW'],
  'manager_approval'=>['APPROVE','APPROVAL'],
  'admin_user_saved'=>['UPDATE','USER'],
  'product_saved'=>['UPDATE','PRODUCT'],
  'product_soft_deleted'=>['SOFT_DELETE','PRODUCT'],
  'master_option_saved'=>['UPDATE','MASTER'],
  'master_option_soft_deleted'=>['SOFT_DELETE','MASTER'],
  'validation_parameter_saved'=>['UPDATE','PARAMETER'],
  'validation_parameter_soft_deleted'=>['SOFT_DELETE','PARAMETER'],
 ];
 if(isset($map[$action])) logActivity($map[$action][0],$map[$action][1],$trial_id,$action,$old_data,$new_data);
}
function notification_target_users($roleTarget=null,$departmentTarget=null,$userId=null){
 $users=[];
 if($userId){
  $s=db()->prepare('SELECT id FROM users WHERE id=? AND is_active=1');
  $s->execute([(int)$userId]);
  return array_map(fn($id)=>(int)$id,$s->fetchAll(PDO::FETCH_COLUMN));
 }
 $roleTarget=trim((string)$roleTarget);
 $departmentTarget=normalize_department($departmentTarget);
 if($roleTarget==='Reviewer'&&$departmentTarget!==''){
  $s=db()->prepare('SELECT id FROM users WHERE is_active=1 AND (UPPER(TRIM(role))=? OR UPPER(TRIM(department))=?)');
  $s->execute([$departmentTarget,$departmentTarget]);
  $users=$s->fetchAll(PDO::FETCH_COLUMN);
 }elseif($roleTarget!==''){
  $s=db()->prepare('SELECT id FROM users WHERE is_active=1 AND role=?');
  $s->execute([$roleTarget]);
  $users=$s->fetchAll(PDO::FETCH_COLUMN);
 }
 return array_map('intval',$users);
}
function createNotification($data){
 try{
  $userId=$data['user_id']??null;
  $roleTarget=trim((string)($data['role_target']??''));
  $departmentTarget=normalize_department($data['department_target']??'');
  $trialId=$data['trial_id']??null;
  $title=trim((string)($data['title']??''));
  $message=trim((string)($data['message']??''));
  $type=trim((string)($data['type']??'info'))?:'info';
  if($title===''||$message==='') return;
  $targets=notification_target_users($roleTarget?:null,$departmentTarget?:null,$userId?:null);
  if(!$targets) $targets=[null];
  foreach($targets as $targetId){
   $check=db()->prepare('SELECT id FROM notifications WHERE user_id <=> ? AND role_target <=> ? AND department_target <=> ? AND trial_id <=> ? AND title=? AND type=? AND is_read=0 LIMIT 1');
   $check->execute([$targetId,$roleTarget?:null,$departmentTarget?:null,$trialId,$title,$type]);
   if($check->fetchColumn()) continue;
   db()->prepare('INSERT INTO notifications(user_id,role_target,department_target,trial_id,title,message,type,is_read,created_at) VALUES(?,?,?,?,?,?,?,0,NOW())')
    ->execute([$targetId,$roleTarget?:null,$departmentTarget?:null,$trialId,$title,$message,$type]);
  }
 }catch(Exception $e){
  // Notifications must never block the main workflow.
 }
}
function notification_scope_sql($alias='n'){
 $userId=(int)(u()['id']??0);
 $where=["$alias.user_id=?"];
 $params=[$userId];
 if(is_admin()){
  $where[]="($alias.user_id IS NULL AND $alias.role_target='Admin')";
 }elseif(role()==='Staff'){
  $where[]="($alias.user_id IS NULL AND $alias.role_target='Staff')";
 }elseif(is_manager_qac()){
  $where[]="($alias.user_id IS NULL AND $alias.role_target='Manager QAC')";
 }elseif(is_reviewer()){
  $departments=review_departments_for_user();
  if($departments){
   $where[]="($alias.user_id IS NULL AND $alias.role_target='Reviewer' AND UPPER(TRIM($alias.department_target)) IN (".implode(',',array_fill(0,count($departments),'?'))."))";
   $params=array_merge($params,$departments);
  }
 }
 return ['('.implode(' OR ',$where).')',$params];
}
function notifications_for_user($limit=5,$filter='all',$offset=0){
 try{
  [$scope,$params]=notification_scope_sql('n');
  $userId=(int)(u()['id']??0);
  $params=array_merge([$userId],$params);
  $where=[$scope,'COALESCE(ns.is_removed,n.removed_by_user,0)=0'];
  if($filter==='unread') $where[]='COALESCE(ns.is_read,n.is_read,0)=0';
  if($filter==='read') $where[]='COALESCE(ns.is_read,n.is_read,0)=1';
  $sql='SELECT n.*,COALESCE(ns.is_read,n.is_read,0) AS is_read,COALESCE(ns.read_at,n.read_at) AS read_at,h.trial_code,h.product_name,h.progress_status,h.current_step FROM notifications n LEFT JOIN notification_user_status ns ON ns.notification_id=n.id AND ns.user_id=? LEFT JOIN trials_header h ON h.id=n.trial_id WHERE '.implode(' AND ',$where).' ORDER BY COALESCE(ns.is_read,n.is_read,0) ASC,n.created_at DESC,n.id DESC';
  if($limit) $sql.=' LIMIT '.(int)$limit.' OFFSET '.(int)$offset;
  $s=db()->prepare($sql);
  $s->execute($params);
  return $s->fetchAll();
 }catch(Exception $e){
  return [];
 }
}
function notifications_for_user_count($filter='all'){
 try{
  [$scope,$params]=notification_scope_sql('n');
  $userId=(int)(u()['id']??0);
  $where=[$scope,'COALESCE(ns.is_removed,n.removed_by_user,0)=0'];
  if($filter==='unread') $where[]='COALESCE(ns.is_read,n.is_read,0)=0';
  if($filter==='read') $where[]='COALESCE(ns.is_read,n.is_read,0)=1';
  $s=db()->prepare('SELECT COUNT(*) FROM notifications n LEFT JOIN notification_user_status ns ON ns.notification_id=n.id AND ns.user_id=? WHERE '.implode(' AND ',$where));
  $s->execute(array_merge([$userId],$params));
  return (int)$s->fetchColumn();
 }catch(Exception $e){
  return 0;
 }
}
function unread_notifications_count(){
 try{
  [$scope,$params]=notification_scope_sql('n');
  $userId=(int)(u()['id']??0);
  $s=db()->prepare('SELECT COUNT(*) FROM notifications n LEFT JOIN notification_user_status ns ON ns.notification_id=n.id AND ns.user_id=? WHERE '.$scope.' AND COALESCE(ns.is_removed,n.removed_by_user,0)=0 AND COALESCE(ns.is_read,n.is_read,0)=0');
  $s->execute(array_merge([$userId],$params));
  return (int)$s->fetchColumn();
 }catch(Exception $e){
  return 0;
 }
}
function setNotificationStatus($notificationId,$read=null,$removed=null){
 $userId=(int)(u()['id']??0);
 if(!$userId) return;
 $current=db()->prepare('SELECT is_read,read_at,is_removed,removed_at FROM notification_user_status WHERE notification_id=? AND user_id=?');
 $current->execute([(int)$notificationId,$userId]);
 $row=$current->fetch()?:['is_read'=>0,'read_at'=>null,'is_removed'=>0,'removed_at'=>null];
 $isRead=$read===null?(int)$row['is_read']:(int)$read;
 $isRemoved=$removed===null?(int)$row['is_removed']:(int)$removed;
 $readAt=$isRead?($row['read_at']?:date('Y-m-d H:i:s')):null;
 $removedAt=$isRemoved?($row['removed_at']?:date('Y-m-d H:i:s')):null;
 db()->prepare('INSERT INTO notification_user_status(notification_id,user_id,is_read,read_at,is_removed,removed_at,created_at) VALUES(?,?,?,?,?,?,NOW()) ON DUPLICATE KEY UPDATE is_read=VALUES(is_read),read_at=VALUES(read_at),is_removed=VALUES(is_removed),removed_at=VALUES(removed_at)')
  ->execute([(int)$notificationId,$userId,$isRead,$readAt,$isRemoved,$removedAt]);
}
function notification_type_class($type){
 $type=trim((string)$type);
 if(in_array($type,['review','approval','approved','rejected','revision','info'],true)) return 'notif-'.$type;
 return 'notif-info';
}
function notification_link($n){
 if(empty($n['trial_id'])) return '/notifications';
 $id=(int)$n['trial_id'];
 $status=$n['progress_status']??'';
 if($status==='In Review'&&is_reviewer()) return '/reviews';
 if($status==='Ready for Approval'&&can_approve_trials()) return '/approval';
 if(in_array($status,['Draft','Need Revision'],true)&&is_staff()) return '/trials/'.$id.'/edit';
 return '/trials/'.$id.'/report';
}
function status_class($status,$final_decision=''){
 if($status==='Draft') return 'status-draft';
 if($status==='In Review') return 'status-review';
 if($status==='Ready for Approval') return 'status-ready';
 if($status==='Approved') return 'status-approved';
 if($status==='Need Revision') return 'status-revision';
 if($status==='Rejected'||$final_decision==='Rejected') return 'status-rejected';
 return 'status-muted';
}
function status_badge($status,$final_decision=''){
 return '<span class="badge '.h(status_class($status,$final_decision)).'">'.h($status).'</span>';
}
function reviewer_has_pending_review($trial_id){
 if(!is_reviewer()) return false;
 $departments=review_departments_for_user();
 $placeholders=implode(',',array_fill(0,count($departments),'?'));
 $s=db()->prepare("SELECT COUNT(*) FROM trials_review tr JOIN trials_header h ON h.id=tr.trial_id WHERE tr.trial_id=? AND tr.status='Pending' AND h.progress_status='In Review' AND tr.review_round=h.revision_no+1 AND UPPER(TRIM(tr.department)) IN ($placeholders)");
 $s->execute(array_merge([$trial_id],$departments));
 return (int)$s->fetchColumn()>0;
}
function trial_action_html($t){
 $id=(int)$t['id'];
 $status=$t['progress_status'];
 $html=[];
 if(reviewer_has_pending_review($id)) return '<a class="btn btn-primary" href="/reviews">Review</a>';
 if(can_edit($t)) $html[]='<a class="btn btn-primary" href="/trials/'.$id.'/validation">Lanjutkan</a><a class="btn btn-light" href="/trials/'.$id.'/edit">Edit</a>';
 elseif(($t['progress_status']??'')==='Draft'&&can_view_trial($t)) $html[]='<a class="btn btn-light" href="/trials/'.$id.'/report">View</a>';
 elseif($status==='In Review') $html[]='<a class="btn btn-light" href="/trials/'.$id.'/report">View Review</a>';
 elseif($status==='Ready for Approval'&&can_approve_trial($t)) $html[]='<a class="btn btn-primary" href="/approval">Approve</a>';
 elseif($status==='Ready for Approval') $html[]='<a class="btn btn-light" href="/trials/'.$id.'/report">View</a>';
 elseif($status==='Approved') $html[]='<a class="btn btn-primary" href="/trials/'.$id.'/report">View Report</a>';
 elseif(in_array($status,['Rejected','Need Revision'],true)||in_array($t['final_decision']??'', ['Rejected','Need Revision'], true)){
  $html[]='<a class="btn btn-light" href="/trials/'.$id.'/report">View</a>';
  if(can_edit($t)) $html[]='<a class="btn btn-primary" href="/trials/'.$id.'/validation">Revise</a>';
 }
 else $html[]='<a class="btn btn-light" href="/trials/'.$id.'/report">View</a>';
 if(is_admin()) $html[]='<form method="post" action="/trials/'.$id.'/delete" class="inline-form" onsubmit="return confirm(\'Delete this trial? Data will be hidden using soft delete.\')"><input type="hidden" name="csrf_token" value="'.h(csrf_token()).'"><button class="danger">Delete</button></form>';
 return implode(' ',$html);
}
function scoped_trials_parts($filters=[],$status_group=null){
 $where=[];
 $params=[];
 $from='trials_header h';
 if(is_reviewer()&&!is_staff()&&!can_approve_trials()){
  $departments=review_departments_for_user();
  $from.=' JOIN trials_review tr_scope ON tr_scope.trial_id=h.id';
  $where[]='UPPER(TRIM(tr_scope.department)) IN ('.implode(',',array_fill(0,count($departments),'?')).')';
  $where[]='(h.progress_status NOT IN ("In Review","Ready for Approval") OR tr_scope.review_round=h.revision_no+1)';
  $params=array_merge($params,$departments);
 }
 $where[]='h.deleted_at IS NULL';
 if(!is_super_admin()){
  $draftScope='h.progress_status<>"Draft" OR LOWER(TRIM(h.created_by))=?';
  $params[]=strtolower(trim((string)(u()['email']??'')));
  try{
   db()->query('SELECT 1 FROM trial_edit_permissions LIMIT 0');
   $draftScope.=' OR EXISTS (SELECT 1 FROM trial_edit_permissions tep WHERE tep.trial_id=h.id AND tep.user_id=? AND tep.can_edit=1 AND tep.revoked_at IS NULL)';
   $params[]=(int)(u()['id']??0);
  }catch(Exception $e){}
  $where[]='('.$draftScope.')';
 }
 if($status_group==='approved') $where[]='h.progress_status="Approved"';
 if($status_group==='in-review') $where[]='h.progress_status="In Review"';
 if($status_group==='need-revision') $where[]='h.progress_status="Need Revision"';
 if($status_group==='rejected') $where[]='(h.progress_status="Rejected" OR h.final_decision="Rejected")';
 if($status_group==='waiting') $where[]='h.progress_status="Ready for Approval"';
 if($status_group==='waiting'&&!is_admin()&&!is_manager_qac()){
  $where[]='h.approver_user_id=?';
  $params[]=(int)(u()['id']??0);
 }elseif($status_group==='waiting'&&!is_admin()){
  $where[]='(h.approver_user_id IS NULL OR h.approver_user_id=?)';
  $params[]=(int)(u()['id']??0);
 }
 if($status_group==='draft') $where[]='h.progress_status="Draft"';

 $q=trim($filters['q']??'');
 if($q!==''){
  $where[]='(h.trial_code LIKE ? OR h.product_name LIKE ? OR h.finish_good_code LIKE ? OR h.product_type LIKE ? OR h.validation_category LIKE ? OR h.validation_scope LIKE ? OR h.machine_used LIKE ?)';
  $like='%'.$q.'%';
  array_push($params,$like,$like,$like,$like,$like,$like,$like);
 }
 if(trim($filters['product_type']??'')!==''){
  $where[]='h.product_type=?';
  $params[]=trim($filters['product_type']);
 }
 if(trim($filters['validation_scope']??'')!==''){
  $where[]='h.validation_scope LIKE ?';
  $params[]='%'.trim($filters['validation_scope']).'%';
 }
 if(trim($filters['machine_used']??'')!==''){
  $where[]='h.machine_used LIKE ?';
  $params[]='%'.trim($filters['machine_used']).'%';
 }
 if(trim($filters['status']??'')!==''){
  $where[]='h.progress_status=?';
  $params[]=trim($filters['status']);
 }
 if(trim($filters['product_name']??'')!==''){
  $where[]='h.product_name LIKE ?';
  $params[]='%'.trim($filters['product_name']).'%';
 }
 if(trim($filters['date_from']??'')!==''&&preg_match('/^\d{4}-\d{2}-\d{2}$/',trim($filters['date_from']))){
  $where[]='h.validation_date>=?';
  $params[]=trim($filters['date_from']);
 }
 if(trim($filters['date_to']??'')!==''&&preg_match('/^\d{4}-\d{2}-\d{2}$/',trim($filters['date_to']))){
  $where[]='h.validation_date<=?';
  $params[]=trim($filters['date_to']);
 }
 return [$from,$where,$params];
}
function scoped_trials_query($filters=[],$status_group=null,$limit=null,$offset=0){
 [$from,$where,$params]=scoped_trials_parts($filters,$status_group);
 $sql='SELECT DISTINCT h.* FROM '.$from;
 if($where) $sql.=' WHERE '.implode(' AND ',$where);
 $sql.=' ORDER BY h.updated_at DESC,h.id DESC';
 if($limit!==null) $sql.=' LIMIT '.(int)$limit.' OFFSET '.(int)$offset;
 $s=db()->prepare($sql);
 $s->execute($params);
 return $s->fetchAll();
}
function scoped_trials_count($filters=[],$status_group=null){
 [$from,$where,$params]=scoped_trials_parts($filters,$status_group);
 $sql='SELECT COUNT(DISTINCT h.id) FROM '.$from;
 if($where) $sql.=' WHERE '.implode(' AND ',$where);
 $s=db()->prepare($sql);
 $s->execute($params);
 return (int)$s->fetchColumn();
}
function trial_summary_counts(){
 $trials=scoped_trials_query();
 $counts=['total'=>count($trials),'draft'=>0,'in_review'=>0,'ready'=>0,'approved'=>0,'need_revision'=>0,'rejected'=>0];
 foreach($trials as $t){
  if($t['progress_status']==='Draft') $counts['draft']++;
  if($t['progress_status']==='In Review') $counts['in_review']++;
  if($t['progress_status']==='Ready for Approval') $counts['ready']++;
  if($t['progress_status']==='Approved') $counts['approved']++;
  if($t['progress_status']==='Need Revision') $counts['need_revision']++;
  if($t['progress_status']==='Rejected'||($t['final_decision']??'')==='Rejected') $counts['rejected']++;
 }
 return $counts;
}
function trial_completeness($t){
 $errors=[];
 $header_required=[
  'batch_number'=>'Batch Number',
  'bulk_code'=>'Bulk Code',
  'support_team'=>'Support Team',
  'initiated_person_team'=>'Initiated person/team',
  'reason'=>'Reason',
  'bom'=>'B.O.M',
 ];
 foreach($header_required as $field=>$label){
  if(trim((string)($t[$field]??''))==='') $errors[]=$label.' wajib diisi.';
 }
 $params=params_for($t['product_type']);
 if(!$params){
  $errors[]='Parameter validation untuk product type '.$t['product_type'].' belum dikonfigurasi.';
 }else{
  $ids=array_column($params,'id');
  $placeholders=implode(',',array_fill(0,count($ids),'?'));
  $s=db()->prepare("SELECT parameter_id,result_value,decision,remark FROM trials_results WHERE trial_id=? AND parameter_id IN ($placeholders)");
  $s->execute(array_merge([$t['id']],$ids));
  $rows=[];
  foreach($s->fetchAll() as $r) $rows[$r['parameter_id']]=$r;
  foreach($params as $p){
   $r=$rows[$p['id']]??null;
   if(!$r||!in_array($r['decision'],['OK','NOT OK','N/A'],true)){
    $errors[]='Parameter '.$p['parameter_name'].' belum memiliki decision.';
    continue;
   }
   if($r['decision']==='NOT OK'&&(trim((string)$r['result_value'])===''||trim((string)$r['remark'])==='')){
    $errors[]='Parameter '.$p['parameter_name'].' NOT OK wajib punya result dan remark.';
   }
  }
 }

 foreach(['Packaging','Filling'] as $section){
  $s=db()->prepare('SELECT item_no,weight_value,is_skipped FROM trials_weighing WHERE trial_id=? AND section=? ORDER BY item_no');
  $s->execute([$t['id'],$section]);
  $rows=$s->fetchAll();
  if(!$rows){
   $errors[]='Weighing '.section_display_name($section).' wajib diisi minimal 1 sample atau diset Skip.';
   continue;
  }
  $skipped=array_filter($rows,fn($r)=>(int)$r['is_skipped']===1);
  $filled=array_filter($rows,fn($r)=>(int)$r['is_skipped']!==1);
  if(count($skipped)>0&&count($filled)===0) continue;
  if(count($skipped)>0){
   $errors[]='Weighing '.section_display_name($section).' tidak boleh campuran Skip dan isi timbang.';
   continue;
  }
  $validCount=0;
  foreach($rows as $r){
   if($r['weight_value']===''||$r['weight_value']===null||!is_numeric($r['weight_value'])){
    $errors[]='Weighing '.section_display_name($section).' item '.$r['item_no'].' belum valid.';
    break;
   }
   $validCount++;
  }
  if($validCount<1) $errors[]='Weighing '.section_display_name($section).' wajib berisi minimal 1 sample.';
 }

 return $errors;
}
