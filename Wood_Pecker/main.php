<?
/* Wood Pecker */

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$Folder = $_GET['destination'];

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'Wood Pecker';
$AppContainer->SecondNameInfo = 'Wood Pecker';
$AppContainer->VersionInfo = '1.5.1';
$AppContainer->AuthorInfo = 'Forest Media';

/* Container Info */
$AppContainer->appName = $AppName;
$AppContainer->appID = $AppID;
$AppContainer->isMobile = $_GET['mobile'];
$AppContainer->LibraryArray = array('bd', 'gui');
$AppContainer->height = '540px';
$AppContainer->width = '600px';
$AppContainer->customStyle = 'padding-top:0px; overflow:unset';
$AppContainer->StartContainer();

$wp_bd = new readbd;
$wp_gui = new gui;

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
$Folder=$_GET['destination'];
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
.wp_menu{
	font-weight: 900;
	font-size: 32px;
	margin: 0 10px;
	cursor: pointer;
	color:#fff;
}

.wp_menu:hover{
	color:#ff7070;
}
</style>
<div id="wp-blocks<?echo $AppID?>" style="display:grid; grid-template-columns:31% 69%; width:100%; height:100%; min-height:400px; min-width:600px;">
  <div id="users<?echo $AppID?>" style="min-height:442px; background: #444753; color:#fff; float:left; height:100%; overflow:auto; overflow-x:hidden;">
		<div id="showmenu<?echo $AppID?>" onClick="showmenu<?echo $AppID?>()" class="ui-forest-blink wp_menu">=</div>
		<div id="wp-left-block<?echo $AppID?>">
			<?
    	foreach ($contacts_file[$_SESSION['loginuser']] as $key => $value){

      	echo $$contacts_file[$_SESSION['loginuser']][$key];
      	echo '<div id="wp_'.$key.'" onclick="wp_load'.$AppID.'('."'wp_sel_user'".',this.id)" class="wp_contacts ui-forest">'.$key.'</div>';
    	}
    	?>
    	<input id="wp_newcontact<?echo $AppID?>" style="margin:10px; width:90%; padding:10px; background:#ececec; border:none;" type="text" placeholder="<? echo $wp_lang['add_label'];?>"/>
    	<div id="wp_addcontactbtn<?echo $AppID?>" onclick="wp_add<?echo $AppID?>('<?echo $wp_sel_user?>')" class="ui-forest-button ui-forest-cancel ui-forest-center" style="width:80%;"><? echo $wp_lang['add_button'];?></div>
		</div>
	</div>
  <div id="messagebox<?echo $AppID?>" style="min-height:400px; background: #ececec; float:right;">
    <div style="background:#dcdcdc; padding:3px; text-align:center; border-bottom:1px solid #ccc; color:#4c4b4b; box-shadow: 1px 1px 4px #ccc; font-variant:all-petite-caps;">
			<?echo $wp_lang['chat_label'].': <b>'.$wp_sel_user.'</b>
			<span class="ui-forest" style="float:right; padding: 1px 5px; cursor: pointer; color: #f3f3f3; background:#fe6f6f; font-size:13px;"  onclick="wp_clear'.$AppID.'(false)">
			'.$wp_lang['clear_button'].'
			</span>';?>
		</div>
		<div style="height:100%; display:grid; grid-template-rows:72% 81%;">
    <div id="messages<?echo $AppID?>" style="min-height:300px; word-break: break-word; padding:5px; overflow:auto; overflow-x:hidden;">
      <?
      foreach ($history_file[$wp_sel_user] as $key => $value){
        $date = str_replace (array('msg','own','own_','msg_'),'',$key);
        $time = str_replace(array('_d','t_','_'),array('','',':'),stristr(stristr($date,'d_'),'t_'));
        $date = str_replace(array('d_','_','_t'),array('','.',''),stristr(stristr($date,'_t',true),'d_'));
        if(!eregi('own',$key)){
          if($history_file[$wp_sel_user][$key] == md5('new_request'.$chat_file_name.$wp_sel_user)){
            $other_message = $wp_lang['message_request'].'<div id="" onclick="wp_clear'.$AppID.'(true)"" class="ui-forest-button ui-forest-accept ui-forest-center">'.$wp_lang['add_button'].'</div>';
          }else{
            $other_message = $history_file[$wp_sel_user][$key];
          }
          echo '
          <div class="wp_msgbubble wp_msgbubble_other">
            <div class="wp_message_info">
              <b>'.$wp_sel_user.'</b> '.$date.', '.$time.'
            </div>
            <div class="wp_msg_bubble wp_msg_bubble_other">
              '.$other_message.'
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
  <div id="sendbox<?echo $AppID?>" style="min-height:100px; height:30%; border-top:1px solid #bbb; background: #e0e0e0;">
    <div id="sendinput<?echo $AppID?>" style="-webkit-user-modify: read-write; width:85%; height:50px; overflow:auto; overflow-x:hidden; word-break:break-word; background:#fff; margin:5px auto; padding:10px;"></div>
    <div id="sendbutton_wp<?echo $AppID?>" onclick="wp_send<?echo $AppID?>('<?echo $wp_sel_user?>')" class="ui-forest-button ui-forest-accept ui-forest-center"><?echo $wp_lang['send_button']?></div>
</div>
  </div>
</div>
</div>

<?

$AppContainer->EndContainer();

?>

<script>
/*--------Логика JS--------*/

function showmenu<?echo $AppID?>(){
	if($("#wp-left-block<?echo $AppID?>").css("display") == "none"){
		$("#wp-left-block<?echo $AppID?>").css("display","block");
		$("#wp-blocks<?echo $AppID?>").css("grid-template-columns","31% 69%");
	}else{
		$("#wp-left-block<?echo $AppID?>").css("display","none");
		$("#wp-blocks<?echo $AppID?>").css("grid-template-columns","7% 93%");
	}
}

showmenu<?echo $AppID?>();

function wp_load<?echo $AppID;?>(key,value){
  clearInterval(timerId<?echo $AppID;?>);
  $("#<?echo $AppID;?>").load("<?echo $Folder?>/main.php?"+key+"="+value+"&id=<?echo rand(0,10000).'&appid='.$AppID.'&mobile='.$click.'&appname='.$AppName.'&dir='.realpath($entry).'&destination='.$Folder;?>")
};


$("#wp_<?echo $wp_sel_user?>").css('background','#6a98fd');
function wp_send<?echo $AppID;?>(value, n_msg){
  if(value){
    var msg_content = '';
    if(n_msg){
      msg_content = n_msg;
    }else{
      msg_content = $("#sendinput<?echo $AppID?>").text();
    }
    $.ajax({
      type: "POST",
      url: "<?echo $Folder;?>sender",
      data: {
         sm:msg_content,
         tu:value,
         o:'own'
      },
      success: function(data){
        status_send = data.replace(/^\s*/,'').replace(/\s*$/,'');
        if (status_send == 'true'){
          wp_newmessage(msg_content,'_own','_own');
          $("#sendinput<?echo $AppID?>").html('');
        }else{
          wp_newmessage('#error: <?echo $wp_lang['message_error'];?>','_error','_own');
          $("#sendinput<?echo $AppID?>").html('');
        }
      }
    });
  }
};

function wp_clear<?echo $AppID;?>(accept){
  var wp_su = "<?echo $wp_sel_user?>";
    $.ajax({
      type: "POST",
      url: "<?echo $Folder;?>clear",
      data: {
         cf:"<?echo $chat_file?>",
         su:wp_su,
         a:accept
      },
      success: function(data){
        status_clear = data.replace(/^\s*/,'').replace(/\s*$/,'');
        if (status_clear == 'true'){
          $("#messages<?echo $AppID?>").html('');
          if(accept==false){
            $("#wp_"+wp_su+"").remove();
          }else{
            wp_send<?echo $AppID?>("<?echo $wp_sel_user?>", "<?echo $wp_lang['message_request_accpet']?>");
          }
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
  $("#messages<?echo $AppID?>").prepend(messagebox);
}

function wp_add<?echo $AppID?>(){
  var new_c = $("#wp_newcontact<?echo $AppID?>").val();
  if(new_c){
    clearInterval(timerId<?echo $AppID?>);
    $("#<?echo $AppID?>").load("<?echo $Folder?>/main.php?new_contact="+new_c+"&id=<?echo rand(0,10000).'&appid='.$AppID.'&mobile='.$click.'&appname='.$AppName.'&dir='.realpath($entry).'&destination='.$Folder;?>");
  }
};

function wp_checker<?echo $AppID?>(){
  var wp_user = "<?echo $wp_sel_user?>";
  var wp_cfm = "<?echo $chat_file_mod?>";
  if(wp_user!=''){
    $.ajax({
      type: "POST",
      url: "<?echo $Folder?>checker",
      data: {
         su:wp_user,
         cfm:wp_cfm
      },
      success: function(data){
        status = data.replace(/^\s*/,'').replace(/\s*$/,'');
        if (status == 'y'){
          clearInterval(timerId<?echo $AppID?>);
          wp_load<?echo $AppID?>('wp_sel_user','<?echo $wp_sel_user?>');
        }
      }
    }).done(function(o) {
  });
  }
}

var timerId<?echo $AppID?> = setInterval(function(){
  if($("#<?echo $AppName.$AppID?>").length){
    wp_checker<?echo $AppID?>();
}else{
  clearInterval(timerId<?echo $AppID?>);
}
},8000);

function closeApp<?echo $AppID?>(){
	clearInterval(timerId<?echo $AppID?>);
}

</script>
<?
unset($AppID);//Очищаем переменную $AppID
?>
