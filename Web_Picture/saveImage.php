<?

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/etc/security.php';
/*--------Run Session--------*/
session_start();
/*--------Check Security--------*/
$security	=	new security;
$security->appprepare();
$data = $_REQUEST['base64data'];
$name = $_REQUEST['name'];
$user = $_REQUEST['user'];
$ext = $_REQUEST['ext'];
if(empty($name)){
  $name = md5(date('d-m-y-h-i-s')).'.'.$ext;
}
if($ext == 'w2p'){
  $image = $data;
}else{
  $image = explode('base64,',$data);
  $image = base64_decode($image[1]);
}
file_put_contents('../../users/'.$user.'/documents/images/'.$name.'.'.$ext,$image);

?>
