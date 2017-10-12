<?
if (function_exists('date_default_timezone_set'))
date_default_timezone_set('Europe/Moscow');
session_start();
$wp_sel_user = $_POST['su'];
$chat_file_mod = $_POST['cfm'];
if(!empty($wp_sel_user) && !empty($chat_file_mod)){
  include '../../core/library/bd.php';
  $wp_bd = new readbd;
  $wp_bd->readglobal2("password","forestusers","login",$_SESSION["loginuser"]);
  $wp_pass = $getdata;
  $chat_file_name = md5($wp_sel_user.$wp_pass.$_SESSION["loginuser"]).'.wpf';
  $doc_dir = '../../users/'.$_SESSION['loginuser'].'/documents';
  $chat_dir = 'Wood_Pecker';
  $chat_file = $doc_dir.'/'.$chat_dir.'/'.$chat_file_name;
  $chat_file_mod_current = md5(date("d.m.y, H:i:s.",filemtime($chat_file)));
  if($chat_file_mod != $chat_file_mod_current){
    echo "y";
  }
}
?>
