<?
/* Telegram Channels */

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$Folder = $_GET['destination'];

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'Telegram Channels';
$AppContainer->SecondNameInfo = 'Telegram Channels';
$AppContainer->VersionInfo = '0.2';
$AppContainer->AuthorInfo = 'Forest Media';

/* Container Info */
$AppContainer->appName = $AppName;
$AppContainer->appID = $AppID;
$AppContainer->LibraryArray = array('bd');
$AppContainer->height = '100%';
$AppContainer->width = '100%';
$AppContainer->customStyle = 'padding-top:0px;';
$AppContainer->StartContainer();

$user = $_SESSION['loginuser'];

$bd = new readbd;
global $security;

$dir = $_SERVER['DOCUMENT_ROOT'].'/system/users/'.$user.'/documents/Telegram_Channels/';
if(!is_dir($dir)){ // check folder
	mkdir($dir);
}


$file_name = md5($bd->readglobal2("password", "forestusers", "login", $user, true).$user);

if(isset($_GET['api']) && isset($_GET['name'])){
	$key = $bd->readglobal2("password", "forestusers", "login", $user, true);
	$api = $security->__encode($_GET['api'], $key);
	$name = $security->__encode($_GET['name'], $key);
	$foc_content = "[configuration]\n\napi='$api'\n\nname='$name'";
	file_put_contents($dir.$file_name.'.foc', $foc_content);
}

	$getAPI = NULL;
	$getName = NULL;

if(is_file($dir.$file_name.'.foc')){
	$key = $bd->readglobal2("password", "forestusers", "login", $user, true);
	$config = parse_ini_file($dir.$file_name.'.foc');
	$getAPI = $security->__decode($config['api'], $key);
	$getName = $security->__decode($config['name'], $key);
}

if(isset($getAPI) && isset($getName) && isset($_GET['send'])){


	$arrayData = array('chat_id' => $getName);

	$message = preg_replace('#%u([0-9A-F]{4})#se','iconv("UTF-16BE","UTF-8",pack("H4","$1"))', $_GET['message']);

	if(isset($_GET['file']) && $_GET['file'] != 'none'){
		$mode = 'send'.$_GET['mode'];
		$type = mb_strtolower($_GET['mode']);
		$arrayData["$type"] = new CURLFile(realpath($_GET['file']));
		$arrayData['caption'] = $message;
	}else{
		$mode = 'sendMessage';
		$arrayData['text'] = $message;
	}

	$url = "https://api.telegram.org/$getAPI/$mode?";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type:multipart/form-data",
	));
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayData);
	$output = curl_exec($ch);

	$output = json_decode($output, true);

}

?>

<style>
.add-file-telegram{
	margin: 5px;
	font-size: 20px;
	font-weight: 900;
	width: max-content;
	min-width: 40px;
	text-align: center;
	background: #8ac9fb;
	color: #05577b;
	padding: 5px;
	border: 2px dashed #1a6daf;
	cursor: pointer;
}

.remove-file-telegram{
	margin: 5px;
	font-size: 15px;
	font-weight: 900;
	width: max-content;
	text-align: center;
	background: #f44336;
	color: #9e150b;
	padding: 5px;
	cursor: pointer;
}
</style>

<div style="min-width: 400px; width: 100%;">
	<div style="display: flex; padding: 10px 5px; background: #2196F3; color: #fff;">
		<div style="padding: 5px;">Bot API Key: </div>
		<input id="APIChannel<?echo $AppID?>" style="border: none; padding: 0 3px; color: #293840; font-size: 15px; filter: contrast(0); transition: all 0.2s ease;" type="text" value="<?echo $getAPI?>" placeholder="bot1234567890:ABCDEFGHIJKLMNOP">
	</div>

	<div style="display: flex; padding: 10px 5px; background: #03A9F4; color: #fff;">
		<div style="padding: 5px;">Имя канала: </div>
		<input id="NameChannel<?echo $AppID?>" style="border: none; padding: 0 3px; color: #293840; font-size: 15px;" type="text" value="<?echo $getName?>" placeholder="@TestChannel">
	</div>

	<div style="padding: 10px 5px; background: #E6E6E6; color: #000;">
		<div style="padding: 5px;">Что опубликовать: </div>
		<div>
			<textarea id="Message<?echo $AppID?>" style="border: none; padding: 5px; color: #000; font-size: 15px; width: 97%; height: 100px; margin: 0 5px;" placeholder="Сообщение / Подпись..."></textarea>
		</div>
		<div>
			<div onClick="addFileTelegram<?echo $AppID?>()" id="addfile<?echo $AppID?>" filesource<?echo $AppID?>="none" class="add-file-telegram ui-forest-blink">+</div>
			<div id="containercheck<?echo $AppID?>" style="display:none; width: 100%;">
				<div onclick="removeFile<?echo $AppID?>()" class="remove-file-telegram ui-forest-blink">Отменить</div>
				Отправить как:
				<select id="documentmode<?echo $AppID?>" name="documentmode<?echo $AppID?>" style="padding: 5px; border: 1px solid #2570ac;">
					<option value="Document" selected>Документ</option>
					<option value="Photo">Изображение</option>
					<option value="Video">Видео</option>
					<option value="Audio">Аудио</option>
					<option value="Animation">Анимация</option>
					<option value="Voice">Голосовое сообщение</option>
				</select>
			</div>
		</div>
		<div id="info<?echo $AppID?>">
		<?
		if(empty($output) && isset($_GET['send'])){
			echo 'Ошибка! Проверьте введеные данные';
		} else if($output['ok'] == 'true'){
			echo 'Cообщение отправлено!';
		}
		?>
	</div>
	</div>

</div>

<div id="sendToChannel<?echo $AppID?>" onClick="sendMessage<?echo $AppID?>()" style="background: #2196F3; color: #FFF; width: max-content; padding: 10px; margin: 15px auto; font-variant: all-petite-caps; font-size: 17px; border-radius: 5px; cursor: pointer; border-bottom: 2px solid #1469AD;" class="ui-forest-blink">Опубликовать</div>

<?
$AppContainer->EndContainer();
?>
<script>

function mouseEnterApp<?echo $AppID?>(){
	var get_file<?echo $AppID?> = $("#addfile<?echo $AppID?>").attr("filesource<?echo $AppID?>");
	if(get_file<?echo $AppID?> != 'none'){
		$("#containercheck<?echo $AppID?>").css('display', 'block');
	}else{
		$("#containercheck<?echo $AppID?>").css('display', 'none');
	}
}

function removeFile<?echo $AppID?>(){
	$("#addfile<?echo $AppID?>").attr("filesource<?echo $AppID?>", '');
	$("#addfile<?echo $AppID?>").text('+');
	$("#containercheck<?echo $AppID?>").css('display', 'none');
}

$("#APIChannel<?echo $AppID?>")
.mouseenter(function(){
	$("#APIChannel<?echo $AppID?>").css('filter', 'contrast(1)');
})
.mouseout(function(){
	$("#APIChannel<?echo $AppID?>").css('filter', 'contrast(0)');
});

<?

// sendMessage
$AppContainer->Event(
	"sendMessage",
	NULL,
	$Folder,
	'main',
	array(
		'api' => '"+escape($("#APIChannel'.$AppID.'").val())+"',
		'name' => '"+escape($("#NameChannel'.$AppID.'").val())+"',
		'message' => '"+escape($("#Message'.$AppID.'").val())+"',
		'send' => 'true',
		'file' => '"+$("#addfile'.$AppID.'").attr("filesource'.$AppID.'")+"',
		'mode' => '"+$("#documentmode'.$AppID.'").val()+"'
	)
);


?>

function addFileTelegram<?echo $AppID?>(){
	data = {callback:"filesource<?echo $AppID?>"};
	makeprocess('system/apps/Explorer/main.php', 'selector', 'explorermode', 'Explorer', JSON.stringify(data));
	$("#info<?echo $AppID?>").html('');
}

</script>
