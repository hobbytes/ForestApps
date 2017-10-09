<?
/*--------Получаем App Name и App ID--------*/
$appname=$_GET['appname'];
$appid=$_GET['appid'];
?>
<div id="<?echo $appname.$appid;?>" style="background-color:#444753; height:500px; width:600px; border-radius:0px 0px 5px 5px; overflow:auto;">
<?php
/*--------Подключаем библиотеки--------*/
include '../../core/library/etc/security.php';
include '../../core/library/bd.php';
$wp_bd = new readbd;
/*--------Запускаем сессию--------*/
session_start();
/*--------Проверяем безопасность--------*/
$security	=	new security;
$security->appprepare();
/*--------Загружаем файл локализации--------*/
$wp_lang  = parse_ini_file('app.lang');
$cl = $_SESSION['locale'];
/*-load chat-*/
$to_user = $_GET['to_user'];
if(isset($to_user)){
  $wp_sel_user = $to_user;
}else{
  $wp_sel_user = str_replace('wp_','',$_GET['wp_sel_user']);
}
$wp_bd->readglobal2("password","forestusers","login",$_SESSION["loginuser"]);
$wp_pass = $getdata;
$chat_file_name = md5($wp_sel_user.$wp_pass.$_SESSION["loginuser"]).'.wpf';
$doc_dir = '../../users/'.$_SESSION['loginuser'].'/documents';
$chat_dir = 'Wood_Pecker';
$chat_file = $doc_dir.'/'.$chat_dir.'/'.$chat_file_name;
if(!is_dir($doc_dir)){
  mkdir($doc_dir);
}
elseif (!is_dir($doc_dir.'/'.$chat_dir)) {
  mkdir($doc_dir.'/'.$chat_dir);
}

$send_message = $_GET['send_message'];
if(!empty($to_user) && !empty($send_message)){
  $date =  date('d_m_y');
  $time = date('h_i_s');
  $json = file_get_contents('http://forest.hobbytes.com/media/os/ubase/getuser.php?login='.$to_user);
  $user_info = json_decode($json ,TRUE);
  $status = file_get_contents('http://'.$user_info['followlink'].'/system/apps/Wood_Pecker/receiver.php?to_user='.$to_user.'&from_user='.$_SESSION['loginuser'].'&send_message='.$send_message.'&d='.$date.'&t='.$time);
    $send_message = preg_replace('#%u([0-9A-F]{4})#se','iconv("UTF-16BE","UTF-8",pack("H4","$1"))',$send_message);
    if (!is_file($chat_file)){
      file_put_contents($chat_file,"[$wp_sel_user]");
    }
    $owner = $_GET['sender'];
    if(!empty($owner)){
      $owner = $owner.'_';
    }

    $get_chat = file_get_contents($chat_file);
    $send_message = '"'.$send_message.'"';
    $send_message = 'msg_'.$owner.'d_'.$date.'_t_'.$time.'='.$send_message;
    $new_chat_file = str_replace("[$wp_sel_user]","[$wp_sel_user]\r\n$send_message",$get_chat);
    file_put_contents($chat_file,$new_chat_file);
}

$contacts_file = 'contacts.foc';
if(!is_file($contacts_file)){
  file_put_contents($contacts_file,'[contacts]');
}
$history_file = parse_ini_file($chat_file,TRUE);
$contacts_file = parse_ini_file('contacts.foc',TRUE);

/*
Инициализируем переменные
$click - переменная используется для определения действия (клик или прикосновение)
$folder - переменная хранит место запуска программы
*/
$click=$_GET['mobile'];
$folder=$_GET['destination'];
/*--------Логика--------*/
?>
<style>
.msgbubble{
  padding:5px;
  display:block;
  width:60%;
  margin:-2px 21px;
  line-height: 26px;
}
.wp_contacts{
  width:100%;
  padding:10px;
  text-align: left;
  font-size: 20px;
  background: #4b5169;
  border-bottom: 2px solid #5d6277;
  cursor:pointer;
  font-variant: all-small-caps;
}
</style>
<div style="width:100%; height:100%; min-height:400px; min-width:600px;">
  <div id="users<?echo $appid?>" style="min-height:442px; background: #444753; color:#fff; width:30%; float:left; height:100%;">
    <?
    foreach ($contacts_file['contacts'] as $key => $value){
      echo '<div id="wp_'.$key.'" onclick="wp_load'.$appid.'('."'wp_sel_user'".',this.id)" class="wp_contacts">'.$key.'</div>';
    }
    ?>
  </div>
  <div id="messagebox<?echo $appid?>" style="min-height:400px; background: #ececec; width:70%; height:100%; float:right;">
    <div id="messages<?echo $appid?>" style="min-height:300px; word-break: break-word; padding:5px; height:70%; overflow:auto; overflow-x:hidden;">
      <?
      foreach ($history_file[$wp_sel_user] as $key => $value){
        $date = str_replace (array('msg','own','own_','msg_'),'',$key);
        $time = str_replace(array('_d','t_','_'),array('','',':'),stristr(stristr($date,'d_'),'t_'));
        $date = str_replace(array('d_','_','_t'),array('','.',''),stristr(stristr($date,'_t',true),'d_'));
        if(!eregi('own',$key)){
          echo '<div class="msgbubble" style="float:left; text-align:left;">
          <div style="font-size: 12px; color:#878787; padding:0 10px;"><b>'.$wp_sel_user.'</b> '.$date.', '.$time.'</div>
          <div style="width:100%; color:#fff; background:#94c2ed; padding:10px; border-radius:10px;">
          '.preg_replace('#%u([0-9A-F]{4})#se','iconv("UTF-16BE","UTF-8",pack("H4","$1"))',$history_file[$wp_sel_user][$key]).'
          </div>
          </div>';
        }else{
        echo '<div class="msgbubble" style="float:right; text-align:right;">
        <div style="font-size: 12px; color:#878787; padding:0 10px;"><b>you</b> '.$date.', '.$time.'</div>
        <div style="width:100%; color:#fff; background:#8bc34a; padding:10px; border-radius:10px;">
        '.preg_replace('#%u([0-9A-F]{4})#se','iconv("UTF-16BE","UTF-8",pack("H4","$1"))',$history_file[$wp_sel_user][$key]).'
        </div>
        </div>';
        }
      }
      ?>
    </div>
  <div id="sendbox<?echo $appid?>" style="min-height:100px; height:30%; border-top:1px solid #bbb; background: #e0e0e0;">
    <div id="sendinput<?echo $appid?>" style="-webkit-user-modify: read-write; width:85%; height:50px; overflow:auto; overflow-x:hidden; word-break:break-word; background:#fff; margin:5px auto; padding:10px;"></div>
    <div id="sendbutton_wp<?echo $appid?>" onclick="wp_send<?echo $appid?>('<?echo $wp_sel_user?>')" class="ui-forest-button ui-forest-accept ui-forest-center">send</div>
</div>
  </div>
</div>
</div>
<script>
/*--------Логика JS--------*/
function wp_load<?echo $appid;?>(key,value){
$("#<?echo $appid;?>").load("<?echo $folder;?>/main.php?"+key+"="+value+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&dir='.realpath($entry).'&destination='.$folder;?>")
};

$("#wp_<?echo $wp_sel_user?>").css('background','#8a4231');
function wp_send<?echo $appid;?>(value){
  if(value){
    $("#<?echo $appid;?>").load("<?echo $folder;?>/main.php?to_user="+value+"&sender=own&send_message="+escape($("#sendinput<?echo $appid?>").text())+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&dir='.realpath($entry).'&destination='.$folder;?>");
  }
};
</script>
<?
unset($appid);//Очищаем переменную $appid
?>
