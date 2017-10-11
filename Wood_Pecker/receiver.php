<?
include '../../core/library/bd.php';
$wp_bd = new readbd;
$send_message = $_POST['send_message'];
$date = $_POST['d'];
$time = $_POST['t'];
$from_user = $_POST['from_user'];
$to_user = $_POST['to_user'];

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
    if (!is_file($chat_file)){
      file_put_contents($chat_file,"[$from_user]");
    }
    $get_chat = file_get_contents($chat_file);
    $send_message = '"'.$send_message.'"';
    $send_message = 'msg_d_'.$date.'_t_'.$time.'='.$send_message;
    $new_chat_file = str_replace("[$from_user]","[$from_user]\r\n$send_message",$get_chat);
    if(file_put_contents($chat_file,$new_chat_file)){
      echo 'true';
    }
}

$contacts_file = $doc_dir.'/'.$chat_dir.'/contacts.foc';
if (!is_file($contacts_file)){
  file_put_contents($contacts_file,"[$to_user]");
}
$get_contacts = file_get_contents($contacts_file);
 if(!eregi($from_user.'=',$get_contacts)){
   $get_contacts = str_replace("[$to_user]","[$to_user]\r\n".$from_user.'=',$get_contacts);
   file_put_contents($contacts_file,$get_contacts);
 }


?>
