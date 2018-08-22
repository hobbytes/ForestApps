<?
/* Web Picture */

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$Folder = $_GET['destination'];

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'Firefox OS - Apps Support';
$AppContainer->SecondNameInfo = 'Firefox OS - Apps Support';
$AppContainer->VersionInfo = '0.1';
$AppContainer->AuthorInfo = 'Forest Media';

/* Container Info */
$AppContainer->appName = $AppName;
$AppContainer->appID = $AppID;
$AppContainer->LibraryArray = array('gui', 'permissions');
$AppContainer->height = '100%';
$AppContainer->width = '100%';
$AppContainer->customStyle = 'padding-top:0px;';
$AppContainer->StartContainer();


$newpermission = new PermissionRequest;
$object = new gui;
$newpermission->fileassociate(array('webapp'), $Folder.'main.php', 'foxloader', $AppName);

$Folder = $_GET['destination'];

$user = $_SESSION['loginuser'];

$file = NULL;

if(isset($_GET['defaultloader'])){
	$file = $_GET['defaultloader'];
	$json = json_decode(file_get_contents($file), true);
}

if(isset($_GET['foxloader'])){
	$file = $_GET['foxloader'];
	$json = json_decode(file_get_contents('http://'.$_SERVER['SERVER_NAME'].$file), true);
}

if(!empty($file)){
	$launch_path = $json['launch_path'];
	$dir = 'http://'.$_SERVER['SERVER_NAME'].str_replace(array('manifest.webapp', $_SERVER['DOCUMENT_ROOT']),'',$file).$launch_path;
	echo '<iframe width="100%" height="100%" class="app-cointainer'.$AppID.'" style="border: 0;" src="'.$dir.'"></iframe>';
}

$AppContainer->EndContainer();
?>
<script>
/*--------JS Logic--------*/

</script>
