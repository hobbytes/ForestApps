<?
/*--------Получаем App Name и App ID--------*/
$appname=$_GET['appname'];
$appid=$_GET['appid'];
?>
<div id="<?echo $appname.$appid;?>" style="background-color:#444753; height:540px; width:600px; border-radius:0px 0px 5px 5px; overflow:hidden;">
<?php
/*--------Подключаем библиотеки--------*/
include '../../core/library/etc/security.php';
include '../../core/library/bd.php';
include '../../core/library/gui.php';
$wp_bd = new readbd;
$wp_gui = new gui;
/*--------Запускаем сессию--------*/
session_start();
/*--------Проверяем безопасность--------*/
$security	=	new security;
$security->appprepare();
/*--------Загружаем файл локализации--------*/
$cl = $_SESSION['locale'];
$wp_lang  = parse_ini_file('lang/'.$cl.'.lang');
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

$contacts_file = $doc_dir.'/'.$chat_dir.'/contacts.foc';
if(!is_file($contacts_file)){
  file_put_contents($contacts_file,'['.$_SESSION['loginuser'].']');
}

$new_contact  = $_GET['new_contact'];
$check_user   = file_get_contents('http://forest.hobbytes.com/media/os/ubase/checkuser.php?check='.$new_contact);
$get_contacts = file_get_contents($contacts_file);
 if(!empty($new_contact) && $check_user == 'true'){
   $this_user = "[".$_SESSION['loginuser']."]";
   if(!eregi($this_user, $get_contacts)){
     file_put_contents($contacts_file,$this_user."\r\n".$get_contacts."\r\n");
   }
   $get_contacts = str_replace($this_user,$this_user."\r\n".$new_contact.'=',$get_contacts);
   file_put_contents($contacts_file,$get_contacts);
 }
 elseif(!empty($new_contact) && $check_user == 'false'){
   $wp_gui->errorLayot($wp_lang['user_error'].': <b>'.$new_contact.'</b>');
 }

$history_file = parse_ini_file($chat_file,TRUE);
$contacts_file = parse_ini_file($doc_dir.'/'.$chat_dir.'/contacts.foc',TRUE);

$chat_file_mod = md5(date("d.m.y, H:i:s.",filemtime($chat_file)));

$click=$_GET['mobile'];
$folder=$_GET['destination'];
/*--------Логика--------*/
?>
<style>
.wp_msgbubble{
  padding:5px;
  display:block;
  width:60%;
  margin:-2px 21px;
  line-height: 26px;
}
.wp_msgbubble_own{
  float:right;
  text-align:right;
}
.wp_msgbubble_other{
  float:left;
  text-align:left;
}
.wp_contacts{
  width:auto;
  padding:15px;
  text-align: left;
  font-size: 20px;
  background: #4b5169;
  border-bottom: 2px solid #5d6277;
  cursor:pointer;
  font-variant: all-small-caps;
  word-wrap: break-word;
}
.wp_message_info{
  font-size: 12px;
  color:#878787;
  padding:0 10px;
}
.wp_message_info b{
  font-variant:all-small-caps;
  font-size:15px;
}
.wp_msg_bubble{
  width:auto;
  min-width:30%;
  max-width:90%;
  color:#fff;
  padding:10px;
  border-radius:10px;
}
.wp_msg_bubble_own{
  background:#8bc34a;
  float:right;
}
.wp_msg_bubble_error{
  background:#e85b50;
  float:right;
}
.wp_msg_bubble_other{
  background:#94c2ed;
  float:left;
}
</style>
<div style="width:100%; height:100%; min-height:400px; min-width:600px;">
  <div id="users<?echo $appid?>" style="min-height:442px; background: #444753; color:#fff; width:30%; float:left; height:100%; overflow:auto; overflow-x:hidden;">
    <?
    foreach ($contacts_file[$_SESSION['loginuser']] as $key => $value){

      echo $$contacts_file[$_SESSION['loginuser']][$key];
      echo '<div id="wp_'.$key.'" onclick="wp_load'.$appid.'('."'wp_sel_user'".',this.id)" class="wp_contacts ui-forest">'.$key.'</div>';
    }
    ?>
    <input id="wp_newcontact<?echo $appid?>" style="margin:10px; width:90%; padding:10px; background:#ececec; border:none;" type="text" placeholder="<? echo $wp_lang['add_label'];?>"/>
    <div id="wp_addcontactbtn<?echo $appid?>" onclick="wp_add<?echo $appid?>('<?echo $wp_sel_user?>')" class="ui-forest-button ui-forest-cancel ui-forest-center" style="width:80%;"><? echo $wp_lang['add_button'];?></div>
  </div>
  <div id="messagebox<?echo $appid?>" style="min-height:400px; background: #ececec; width:70%; height:100%; float:right;">
    <div style="background:#dcdcdc; padding:3px; text-align:center; border-bottom:1px solid #ccc; color:#4c4b4b; box-shadow: 1px 1px 4px #ccc; font-variant:all-petite-caps;"><?echo $wp_lang['chat_label'].': <b>'.$wp_sel_user.'</b><span class="ui-forest" style="float:right; padding: 1px 5px; cursor: pointer; color: #f3f3f3; background:#fe6f6f; font-size:13px;"  onclick="wp_clear'.$appid.'()">'.$wp_lang['clear_button'].'</span>';?></div>
    <div id="messages<?echo $appid?>" style="min-height:300px; word-break: break-word; padding:5px; height:70%; overflow:auto; overflow-x:hidden;">
      <?
      foreach ($history_file[$wp_sel_user] as $key => $value){
        $date = str_replace (array('msg','own','own_','msg_'),'',$key);
        $time = str_replace(array('_d','t_','_'),array('','',':'),stristr(stristr($date,'d_'),'t_'));
        $date = str_replace(array('d_','_','_t'),array('','.',''),stristr(stristr($date,'_t',true),'d_'));
        if(!eregi('own',$key)){
          echo '
          <div class="wp_msgbubble wp_msgbubble_other">
            <div class="wp_message_info">
              <b>'.$wp_sel_user.'</b> '.$date.', '.$time.'
            </div>
            <div class="wp_msg_bubble wp_msg_bubble_other">
              '.$history_file[$wp_sel_user][$key].'
            </div>
          </div>';
        }else{
        echo '
        <div class="wp_msgbubble wp_msgbubble_own">
          <div class="wp_message_info">
            <b>'.$wp_lang['own_label'].'</b> '.$date.', '.$time.'
          </div>
          <div class="wp_msg_bubble wp_msg_bubble_own">
            '.$history_file[$wp_sel_user][$key].'
          </div>
        </div>';
        }
      }
      ?>
    </div>
  <div id="sendbox<?echo $appid?>" style="min-height:100px; height:30%; border-top:1px solid #bbb; background: #e0e0e0;">
    <div id="sendinput<?echo $appid?>" style="-webkit-user-modify: read-write; width:85%; height:50px; overflow:auto; overflow-x:hidden; word-break:break-word; background:#fff; margin:5px auto; padding:10px;"></div>
    <div id="sendbutton_wp<?echo $appid?>" onclick="wp_send<?echo $appid?>('<?echo $wp_sel_user?>')" class="ui-forest-button ui-forest-accept ui-forest-center"><?echo $wp_lang['send_button']?></div>
</div>
  </div>
</div>
</div>
<script>
/*--------Логика JS--------*/
function wp_load<?echo $appid;?>(key,value){
  clearInterval(timerId<?echo $appid;?>);
  $("#<?echo $appid;?>").load("<?echo $folder;?>/main.php?"+key+"="+value+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&dir='.realpath($entry).'&destination='.$folder;?>")
};

$("#wp_<?echo $wp_sel_user?>").css('background','#6a98fd');
function wp_send<?echo $appid;?>(value){
  if(value){
    var msg_content = $("#sendinput<?echo $appid?>").text();
    $.ajax({
      type: "POST",
      url: "<?echo $folder;?>sender",
      data: {
         sm:msg_content,
         tu:value,
         o:'own'
      },
      success: function(data){
        status_send = data.replace(/^\s*/,'').replace(/\s*$/,'');
        if (status_send == 'true'){
          wp_newmessage(msg_content,'_own','_own');
          $("#sendinput<?echo $appid?>").html('');
        }else{
          wp_newmessage('#error: <?echo $wp_lang['message_error'];?>','_error','_own');
          $("#sendinput<?echo $appid?>").html('');
        }
      }
    });
  }
};

function wp_clear<?echo $appid;?>(value){
  var wp_su = "<?echo $wp_sel_user?>";
    $.ajax({
      type: "POST",
      url: "<?echo $folder;?>clear",
      data: {
         cf:"<?echo $chat_file?>",
         su:wp_su
      },
      success: function(data){
        status_clear = data.replace(/^\s*/,'').replace(/\s*$/,'');
        if (status_clear == 'true'){
          $("#messages<?echo $appid?>").html('');
          $("#wp_"+wp_su+"").remove();
        }
      }
    });
};

function wp_newmessage(message_content,type,owner){
  var today = new Date();
  var date_time = $.datepicker.formatDate("d.m.y, ", today) + today.getHours() + ':' + today.getMinutes() + ':' + today.getSeconds();
  var messageinfo = $("<div/>").addClass("wp_message_info").html("<b>you</b> " + date_time);
  var message = $("<div/>").addClass("wp_msg_bubble wp_msg_bubble" + type).html(message_content);
  var messagebox = $("<div/>").addClass("wp_msgbubble wp_msgbubble" + owner).html(message);
  messagebox.prepend(messageinfo);
  $("#messages<?echo $appid?>").prepend(messagebox);
}

function wp_add<?echo $appid;?>(){
  var new_c = $("#wp_newcontact<?echo $appid;?>").val();
  if(new_c){
    clearInterval(timerId<?echo $appid;?>);
    $("#<?echo $appid;?>").load("<?echo $folder;?>/main.php?new_contact="+new_c+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&dir='.realpath($entry).'&destination='.$folder;?>");
  }
};

function wp_checker<?echo $appid;?>(){
  var wp_user = "<?echo $wp_sel_user?>";
  var wp_cfm = "<?echo $chat_file_mod?>";
  if(wp_user!=''){
    $.ajax({
      type: "POST",
      url: "<?echo $folder;?>checker",
      data: {
         su:wp_user,
         cfm:wp_cfm
      },
      success: function(data){
        status = data.replace(/^\s*/,'').replace(/\s*$/,'');
        if (status == 'y'){
          clearInterval(timerId<?echo $appid;?>);
          wp_load<?echo $appid;?>('wp_sel_user','<?echo $wp_sel_user?>');
        }
      }
    }).done(function(o) {
  });
  }
}

var timerId<?echo $appid;?> = setInterval(function(){
  if($("#<?echo $appname.$appid;?>").length){
    wp_checker<?echo $appid;?>();
}else{
  clearInterval(timerId<?echo $appid;?>);
}
},8000);
</script>
<?
unset($appid);//Очищаем переменную $appid
?>
