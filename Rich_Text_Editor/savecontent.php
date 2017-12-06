<?
include '../../core/library/etc/security.php';
$security	=	new security;
$security->appprepare();
session_start();
if(isset($_SESSION['loginuser'])){
  $content = $_POST['content'];
  $folder = $_POST['folder'];
  if(eregi('/system/core/',$folder) || eregi('os.php',$folder) || eregi('login.php',$folder)){
    if($_SESSION['loginuser'] != $_SESSION['superuser']){
      $folder = '';
      $content  = '';
    }
  }
  if (!empty($folder)) {
    $myfile=fopen($folder,  "w");
    fwrite($myfile, $content);
    fclose($myfile);
  }
}else{
  exit;
}
?>
