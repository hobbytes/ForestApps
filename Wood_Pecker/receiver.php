<?
include '../../core/library/bd.php';
$wp_bd = new readbd;

$send_message = $_GET['send_message'];
$date = $_GET['d'];
$time = $_GET['t'];
$from_user = $_GET['from_user'];
$to_user = $_GET['to_user'];

$wp_bd->readglobal2("password","forestusers","login",$to_user);
$wp_pass = $getdata;
$chat_file_name = md5($from_user.$wp_pass.$to_user).'.wpf';
$doc_dir = '../../users/'.$to_user.'/documents';
$chat_dir = 'Wood_Pecker';
$chat_file = $doc_dir.'/'.$chat_dir.'/'.$chat_file_name;
if(!is_dir($doc_dir)){
  mkdir($doc_dir);
}
elseif (!is_dir($doc_dir.'/'.$chat_dir)) {
  mkdir($doc_dir.'/'.$chat_dir);
}

if(!empty($from_user) && !empty($send_message)){
    $send_message = preg_replace('#%u([0-9A-F]{4})#se','iconv("UTF-16BE","UTF-8",pack("H4","$1"))',$send_message);
    if (!is_file($chat_file)){
      file_put_contents($chat_file,"[$from_user]");
    }
    $get_chat = file_get_contents($chat_file);
    $send_message = '"'.$send_message.'"';
    $send_message = 'msg_d_'.$date.'_t_'.$time.'='.$send_message;
    $new_chat_file = str_replace("[$from_user]","[$from_user]\r\n$send_message",$get_chat);
    file_put_contents($chat_file,$new_chat_file);
}

$contacts_file = 'contacts.foc';
$get_contacts = file_get_contents($contacts_file);
 if(!eregi($from_user.'=',$get_contacts)){
   file_put_contents($contacts_file,"[contacts]\r\n".$from_user.'=');
 }

echo 'true';

?>
