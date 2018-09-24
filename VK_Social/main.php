<?
/* VK Social */

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$Folder = $_GET['destination'];

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'VK Social';
$AppContainer->SecondNameInfo = 'VK Social';
$AppContainer->VersionInfo = '1.1.2';
$AppContainer->AuthorInfo = 'Forest Media';

/* Container Info */
$AppContainer->appName = $AppName;
$AppContainer->appID = $AppID;
$AppContainer->LibraryArray = array('gui', 'bd');
$AppContainer->height = '100%';
$AppContainer->width = '100%';
$AppContainer->customStyle = 'padding-top:0px; height:100%;';
$AppContainer->StartContainer();

$settingsbd = new readbd;
$gui = new gui;

/*--------Логика--------*/


$fuid = $settingsbd->readglobal2("fuid","forestusers","login",$_SESSION["loginuser"], true);
$password = $settingsbd->readglobal2("password","forestusers","login",$_SESSION["loginuser"], true);
$d_root = $_SERVER['DOCUMENT_ROOT'];
$token = md5($fuid.$d_root.$password);
$hash = md5(date('d.m.y h:i:s'));
$get_domain = $_GET['domain'];

$data = http_build_query(array('token' => $token, 'id' => $get_domain, 'h' => $hash));
$get_json = file_get_contents('http://forest.hobbytes.com/media/os/modules/social.php?'.$data);
$json = json_decode($get_json, TRUE);

?>
<style>
.s-container{
  background-color: #fff;
  border-bottom: 1px solid #ddd;
  padding:  10px;
}
.name-container{
  color: #8e8e8e;
	padding: 5px 0;
}
.in-container{
  text-transform: uppercase;
  display: inline-block;
  width:  100px;
  height: 50px;
  margin: 10px;
  border: 1px solid #ccc;
  padding: 15px;
  font-size:  12px;
  text-align: center;
}

.in-value{
  margin: 10px;
  font-size: 20px;
  font-weight:600;
}

.a-button{
  color:#fff;
  font-size:12px;
  background-color:#5f86c4;
  cursor:pointer;
  padding: 4 10px;
  border-radius:5px;
  margin:auto 5px;
}

.gray-label{
  color:#8e8e8e;
  font-size: 12px;
}

.vk-user<?echo $AppID?>{
	padding:10px 0;
}

.following-vk{
  padding: 5px;
  font-weight: 900;
  color: #171717;
  font-variant: all-petite-caps;
  font-size: 18px;
  float:left;
}
</style>

<script>

<?
// add domain
$AppContainer->Event(
	"add_domain",
	"el",
	$Folder,
	'main',
	array(
		'domain' => '"+$("#'.$AppID.'domain").val()+"'
	)
);

?>

function ShowSettings<?echo $AppID?>(){
  if($("#settings<?echo $AppID?>").is( ":hidden" )){
    $("#settings<?echo $AppID?>").slideDown("fast");
  }else{
    $("#settings<?echo $AppID?>").slideUp("fast");
  }
}

function AddFollow<?echo $AppID?>(){

  hashCode = "<?echo substr($token, -13)?>"+$('#follow<?echo $AppID?>').val();
  nameunit = $('#follow<?echo $AppID?>').val();

  $.ajax({
    type: "GET",
    url: "http://forest.hobbytes.com/media/os/modules/vk/AddNew",
    data: {
       rl:"<?echo 'http://'.$_SERVER['SERVER_NAME'].'/system/apps/Remote_Lab/hub.php?user='.$_SESSION['loginuser'].'&token='?>"+hashCode,
       id:$('#follow<?echo $AppID?>').val(),
       token:"<?echo $token?>"
    },
    success: function(data){
      status = data.replace(/^\s*/,'').replace(/\s*$/,'');
      $("#errorid<?echo $AppID?>").html(decodeURIComponent(status));
      if(!status){
        $('#followlist<?echo $AppID?>').html($('#followlist<?echo $AppID?>').html()+'<div style="display: flex; margin: 10px 0;" id="user<?echo $AppID?>'+$('#follow<?echo $AppID?>').val()+'"><div class="following-vk">'+$('#follow<?echo $AppID?>').val()+'</div><div onClick="DeleteFollow<?echo $AppID?>(\''+$("#follow<?echo $AppID?>").val()+'\')" class="ui-forest-button ui-forest-cancel" style="float:left;">Удалить</div></div>');
        $('#follow<?echo $AppID?>').val('');
        data = {customname:hashCode};
        makeprocess('system/apps/Remote_Lab/main.php', "vk-"+nameunit, 'addunit',  'Remote_Lab', JSON.stringify(data));
      }
    }
  });
}

function DeleteFollow<?echo $AppID?>(userfollow){

  $.ajax({
    type: "GET",
    url: "http://forest.hobbytes.com/media/os/modules/vk/DeleteUser",
    data: {
       id:userfollow,
       token:"<?echo $token?>"
    },
    success: function(data){
      $("#user<?echo $AppID?>"+userfollow).remove();
    }
  });
}



</script>
<div style="width:100%; height:auto">
  <?
    if($json['online']  ==  0){
      $online = 'не в сети - '.$json['last_seen'];
    }elseif($json['online']  ==  1){
      $online = 'онлайн';
    }
    echo '<div style="padding:10px; background-color:#5f86c4; color:#fff; display: grid; grid-template-columns: 50% 50%;">
    <div>
      '.$json['first_name'].' '.$json['last_name'].'
      <div style="color:#d6d6d6; font-size:13px;">'.$online.'</div>
    </div>
    <div style="margin: 12px 0; text-align: right;">
    <input id="'.$AppID.'domain" style="border:1px solid #ccc; font-size:20px; border-radius:5px; width: 190px; padding: 5px;" value="'.$get_domain.'" type="text" name="'.$AppID.'domain" placeholder="введите id или ник">
    <div onClick="ShowSettings'.$AppID.'()" class="ui-forest-button ui-forest-accept" style="float: right;">=</div>
    <div onClick="add_domain'.$AppID.'()" class="ui-forest-button ui-forest-accept" style="margin: 0 10px; float: right;">Поиск</div>
    </div>
    </div>';

    //settings
    echo '<div id="settings'.$AppID.'" style="width: 80%; height: 60%; position: fixed; background: #fdfdfd; border: 1px solid #dedede; margin: 5% auto; left: 0; right: 0; overflow-y: auto; border-radius: 4px; display: none; box-shadow: 1px 1px 4px rgba(0,0,0,0.5); padding: 20px;">';

    echo '<div style="font-size: 40px; font-weight: 600; color: #5f86c4; padding: 17px 0;">Добавить</div>';

    if(!is_file($_SERVER['DOCUMENT_ROOT'].'/system/apps/Remote_Lab/main.php')){
      echo '<div style="padding: 10px 0; color: #b7423a;"> Чтобы отслеживать пользователя, необходима программа <b>"Remote Lab"</b>. Ее можно скачать в магазине приложений. </div>';
    }

    echo '<div style="display: table;"><input id="follow'.$AppID.'" style="border:1px solid #ccc; font-size:20px; border-radius:5px; width: 190px; padding: 5px; float: left;" value="'.$get_domain.'" type="text" name="'.$AppID.'domain" placeholder="введите id или ник">';
    echo '<div onClick="AddFollow'.$AppID.'()" class="ui-forest-button ui-forest-accept" style="float: left; margin: 0 10px;">Отслеживать</div>';
    echo '<div style="float: right; padding: 10px 0;" id="errorid'.$AppID.'"></div></div>';

    echo '<div style="font-size: 40px; font-weight: 600; color: #5f86c4; padding: 17px 0; margin-top: 17px;">Список отслеживаемых</div>';

    echo '<div id="followlist'.$AppID.'" style="width: 98%; background: #e4e4e4; padding: 5px; border: 1px solid #ccc;">';
    $data_ = http_build_query(array('token' => $token, 'id' => '1'));
    $get_json_list = file_get_contents('http://forest.hobbytes.com/media/os/modules/vk/GetList.php?'.$data_);
    $json_list = json_decode($get_json_list, TRUE);

    foreach($json_list as $key){
      echo '<div style="display: flex; margin: 10px 0;" id="user'.$AppID.$key.'"><div class="following-vk">'.$key.'</div><div onClick="DeleteFollow'.$AppID.'(\''.$key.'\')" class="ui-forest-button ui-forest-cancel" style="float:left;">Удалить</div></div>';
    }

    echo '</div>';

    echo '<div style="padding: 40px 0; color: #a2a2a2;">'.$AppContainer->AppNameInfo.' version '.$AppContainer->VersionInfo.'</div>';

    echo '</div>';

  if(!empty($json['error'])){
    if(empty($get_domain)){
      die();
    }else{
      die($gui->errorLayot($json['error']));
    }
  }else{
    function dataContainer($name, $data){
      if(!empty($data)){
        echo '<div class="s-container"><div class="name-container">'.$name.'</div>'.$data.'</div>';
      }
    }

    function inBlock($lablel, $value, $customClass = NULL){
      echo '<div class="in-container">
      '.$lablel.'
      <div class="in-value '.$customClass.'">
      '.$value.'
      </div></div>';
    }

    //how old
    $btimestamp = strtotime($json['bdate']);
    $age = date('Y') - date('Y', $btimestamp);
    if(date('md', $btimestamp) > date('md')){
      $age--;
    }

		//make beauty date
		$bdate = new DateTime($json['bdate']);
		$bdate = $bdate->format('d.m.y');
		if($bdate == date('d.m.y')){
			$bdate = 'неизвестно';
		}

    //gender
    $get_sex = $json['sex'];
    $gender = '';
    if(!empty($get_sex)){
      switch($get_sex){
        case 0:
            $gender = 'пол не указан';
            break;
        case 1:
            $gender = 'женский';
            break;
        case 2:
            $gender = 'мужской';
            break;
      }
    }

    //relation
    $get_relation = $json['relation'];
    if(!empty($get_relation) && is_int($get_relation)){
      switch($get_relation){
        case 0:
            $relation = 'не указано';
            break;
        case 1:
            $relation = 'не женат/не замужем';
            break;
        case 2:
            $relation = 'есть друг/есть подруга';
            break;
        case 3:
            $relation = 'помовлен/помовлена';
            break;
        case 4:
            $relation = 'женат/замужем';
            break;
        case 5:
            $relation = 'все сложно';
            break;
        case 6:
            $relation = 'в активном поиске';
            break;
        case 7:
            $relation = 'влюблен/влюблена';
            break;
        case 8:
            $relation = 'в гражданском браке';
            break;
      }
    }else{
      $relation = str_replace(array("#o","#c"),array("<span class='gray-label'>",'</span>'),$get_relation);
    }

    $fidelity = '';
    if(!preg_match('/0%/i',$json['fidelity'])){
      $fidelity = str_replace(array("#o","#c"),array("<span class='gray-label'>",'</span>'),$json['fidelity']);
    }

    echo '<div style="margin:10px 0;">';
    echo '<img src="'.$json['small_photo'].'" style="padding:10px; border-radius:60px; width:100px; height:100px; object-fit: cover;" class="ui-forest-blink" onClick="makeprocess(\'system/apps/Image_Viewer/main.php\',\''.$json['large_photo'].'\',\'photoviewload\', \'Image_Viewer\')">';
    dataContainer('Имя', $json['first_name'].' '.$json['last_name']);
    dataContainer('id', $json['id'].' | '.$json['domain']);
    dataContainer('Дата рождения', $bdate . ' ('.$age.')');
    dataContainer('Пол', $gender);
    dataContainer('Отношения', $relation);
    dataContainer('Верность партнеру', $fidelity);
    dataContainer('Город', $json['country'].', '.$json['home_town']);
    dataContainer('Интересы', $json['interests']);
    echo '</div>';
    echo '<div style="margin:10px 0;">';
    dataContainer('Номер телефона', $json['mobile_phone']);
    dataContainer('Twitter', $json['twitter']);
    dataContainer('Facebook', $json['facebook']);
    dataContainer('Instagram', $json['instagram']);
    echo '</div>';
    echo '<div style="margin:10px 0;">';
    dataContainer('Ключевые слова', $json['keywords']);
    echo '</div>';
    echo '<div style="margin:10px 0;"><div class="s-container"><div class="name-container">Возможные родственники</div>';
    $i=0;
    foreach ($json['family'] as $key)
    {
      for ($i = 0; $i < count($key); $i++) {
        if(!empty($key[$i]['id'])){
          echo '<div>'.$key[$i]['first_name'].' '.$key[$i]['last_name'].'<span class="a-button ui-forest-blink" onClick="makeprocess(\'system/apps/VK_Social/main.php\',\''.$key[$i]['id'].'\',\'domain\', \''.$AppName.'\')"> выбрать </span></div><br>';
        }
      }
    }
    echo '</div></div>';

    echo '<div style="margin:10px 0;"><div class="s-container"><div class="name-container">Друзья</div>';

    inBlock('Всего друзей',  $json['count']);
    inBlock('В сети',  0, 'onlineCount'.$AppID);
    inBlock('Мужчин',  $json['sexCount']['m']);
    inBlock('Женщин',  $json['sexCount']['w']);
    inBlock('Неизвестно',  $json['sexCount']['u']);

    echo '<div id="showallfriends'.$AppID.'" class="ui-forest-blink" style="cursor:pointer; text-align: center; margin: 5px; padding:10px; border: 2px solid #5f86c4; background: #abc3ea; color: #1f375f;">Показать всех друзей</div>';
    echo '<div id="allfriends'.$AppID.'" style="display:none; padding: 7px;">';
		echo '<div onClick="ShowHideOnline'.$AppID.'()" class="ui-forest-button ui-forest-accept show-button'.$AppID.'" style="margin: 10px 0px;">Показать/Скрыть онлайн</div>';

		$onlineCount = 0;
    foreach ($json['friends'] as $key)
    {
      for ($i = 0; $i < count($key); $i++) {
        if(!empty($key[$i]['id'])){
          if($key[$i]['online'] == 1){
            $online = ' <span style="color: #f44336;">[online]</span>';
						$onlineTag = 'vk-online'.$AppID;
						$onlineCount++;
          }else{
						$online = '';
						$onlineTag = '';
					}

          echo '<div class="vk-user'.$AppID.' '.$onlineTag.'">'.$key[$i]['first_name'].' '.$key[$i]['last_name'].$online.'<span class="a-button ui-forest-blink" onClick="makeprocess(\'system/apps/VK_Social/main.php\',\''.$key[$i]['id'].'\',\'domain\', \''.$AppName.'\')"> выбрать </span></div>';
        }
      }
    }
    echo '
		</div>
		</div>
		</div>';
  }

echo '</div>';
$AppContainer->EndContainer();

?>
<script>
$(".onlineCount<?echo $AppID?>").html('<?echo $onlineCount?>');

let show<?echo $AppID?> = false;

function ShowHideOnline<?echo $AppID?>(){
	if(!show<?echo $AppID?>){
		$('.vk-user<?echo $AppID?>').css('display','none');
		$('.vk-online<?echo $AppID?>').css('display','block');
		$('.show-button<?echo $AppID?>').switchClass('ui-forest-accept', 'ui-forest-cancel', 500);
		show<?echo $AppID?> = true;
	}else{
		$('.show-button<?echo $AppID?>').switchClass('ui-forest-cancel', 'ui-forest-accept', 500);
		$('.vk-user<?echo $AppID?>').css('display','block');
		show<?echo $AppID?> = false;
	}
}

$("#showallfriends<?echo $AppID?>").click(function(){
  if($("#allfriends<?echo $AppID?>").is( ":hidden" )){
    $("#showallfriends<?echo $AppID?>").text('Скрыть всех друзей');
    $("#allfriends<?echo $AppID?>").slideDown("fast");
  }else{
    $("#showallfriends<?echo $AppID?>").text('Показать всех друзей');
    $("#allfriends<?echo $AppID?>").slideUp("fast");
}
});
UpdateWindow("<?echo $AppID?>","<?echo $AppName?>");
</script>
<?
unset($AppID);
?>
