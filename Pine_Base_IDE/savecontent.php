<?
include '../../core/library/etc/security.php';
$security	=	new security;
$security->appprepare();
session_start();
if(isset($_SESSION['loginuser'])){
  $content = $_POST['content'];
  $folder = $_POST['folder'];

  $myfile=fopen($folder,  "w");
  fwrite($myfile, $content);
  fclose($myfile);
}else{
  exit;
}
?>
