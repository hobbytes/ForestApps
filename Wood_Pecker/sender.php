<?
if (function_exists('date_default_timezone_set'))
date_default_timezone_set('Europe/Moscow');

include '../../core/library/bd.php';
include '../../core/library/etc/security.php';
$wp_bd = new readbd;
session_start();
$security	=	new security;
$security->appprepare();

$to_user = $_POST['tu'];
$wp_sel_user = $to_user;
$send_message = $_POST['sm'];

$wp_bd->readglobal2("password","forestusers","login",$_SESSION["loginuser"]);
$wp_pass = $getdata;
$chat_file_name = md5($to_user.$wp_pass.$_SESSION["loginuser"]).'.wpf';
$doc_dir = '../../users/'.$_SESSION["loginuser"].'/documents';
$chat_dir = 'Wood_Pecker';
$chat_file = $doc_dir.'/'.$chat_dir.'/'.$chat_file_name;


if(!empty($to_user) && !empty($send_message)){
  $date =  date('d_m_y');
  $time = date('H_i_s');
  $json = file_get_contents('http://forest.hobbytes.com/media/os/ubase/getuser.php?login='.$to_user);
  $user_info = json_decode($json, TRUE);
  if(empty($user_info['followlink'])){
    die('false');
  }
  include '../../core/library/etc/http.php';
  $new_request = new http;
  $status = $new_request->makeNewRequest('http://'.$user_info['followlink'].'/system/apps/Wood_Pecker/receiver','Wood Pecker Chat',$data = array('tu' => $to_user, 'fu' => $_SESSION['loginuser'], 'sm' => $send_message, 'd' => $date, 't' => $time));
  if(eregi('true',$status)){
    if (!is_file($chat_file)){
      file_put_contents($chat_file,"[$wp_sel_user]");
    }
    $owner = $_POST['o'];
    if(!empty($owner)){
      $owner = $owner.'_';
    }
    $get_chat = file_get_contents($chat_file);
    $send_message = '"'.$send_message.'"';
    $send_message = 'msg_'.$owner.'d_'.$date.'_t_'.$time.'='.$send_message;
    $new_chat_file = str_replace("[$wp_sel_user]","[$wp_sel_user]\r\n$send_message",$get_chat);
    file_put_contents($chat_file,$new_chat_file);
    echo 'true';
}else{
  echo 'false';
}
}
?>
