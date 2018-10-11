<?
/* Web App */

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$Folder = $_GET['destination'];

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'Telegram Channels';
$AppContainer->SecondNameInfo = 'Telegram Channels';
$AppContainer->VersionInfo = '0.1';
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

$tempDir ='./temp';
if(!is_dir($tempDir)){
	mkdir($tempDir);
}

$dir = $_SERVER['DOCUMENT_ROOT'].'/system/users/'.$user.'/documents/Telegram_Channels/';
if(!is_dir($dir)){ // check folder
	mkdir($dir);
}

if(isset($_GET['api']) && isset($_GET['name'])){
	$key = $bd->readglobal2("password", "forestusers", "login", $user, true);
	$api = $security->__encode($_GET['api'], $key);
	$name = $security->__encode($_GET['name'], $key);
	$foc_content = "[configuration]\n\napi='$api'\n\nname='$name'";
	file_put_contents($dir.'config.foc', $foc_content);
}

	$getAPI = NULL;
	$getName = NULL;

if(is_file($dir.'config.foc')){
	$key = $bd->readglobal2("password", "forestusers", "login", $user, true);
	$config = parse_ini_file($dir.'config.foc');
	$getAPI = $security->__decode($config['api'], $key);
	$getName = $security->__decode($config['name'], $key);
}

if(isset($getAPI) && isset($getName) && isset($_GET['send'])){
	$data = http_build_query(array('chat_id' => $getName, 'text' => $_GET['message']));
	$status = file_get_contents("https://api.telegram.org/$getAPI/sendMessage?".$data);
	$json = json_decode($status, true);
}

?>
<div style="width: 400px;">
	<div style="display: flex; padding: 10px 5px; background: #2196F3; color: #fff;">
		<div style="padding: 5px;">Bot API Key: </div>
		<input id="APIChannel<?echo $AppID?>" style="border: none; padding: 0 3px; color: #293840; font-size: 15px;" type="text" value="<?echo $getAPI?>" placeholder="bot1234567890:ABCDEFGHIJKLMNOP">
	</div>

	<div style="display: flex; padding: 10px 5px; background: #03A9F4; color: #fff;">
		<div style="padding: 5px;">Имя канала: </div>
		<input id="NameChannel<?echo $AppID?>" style="border: none; padding: 0 3px; color: #293840; font-size: 15px;" type="text" value="<?echo $getName?>" placeholder="@TestChannel">
	</div>

	<div style="padding: 10px 5px; background: #E6E6E6; color: #000;">
		<div style="padding: 5px;">Что опубликовать: </div>
		<div>
			<textarea id="Message<?echo $AppID?>" style="border: none; padding: 5px; color: #000; font-size: 15px; width: 97%; height: 100px; margin: 0 5px;" placeholder="Сообщение..."></textarea>
		</div>
		<?
		if(empty($json) && isset($_GET['send'])){
			echo 'Ошибка! Проверьте введеные данные';
		} else if($json['ok'] == '1'){
			echo 'Cообщение отправлено!';
		}
		?>
	</div>

</div>

<div id="sendToChannel<?echo $AppID?>" onClick="sendMessage<?echo $AppID?>()" style="background: #2196F3; color: #FFF; width: max-content; padding: 10px; margin: 15px auto; font-variant: all-petite-caps; font-size: 17px; border-radius: 5px; cursor: pointer; border-bottom: 2px solid #1469AD;" class="ui-forest-blink">Опубликовать</div>
<?
$AppContainer->EndContainer();
?>
<script>

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
		'send' => 'true'
	)
);


?>

</script>
