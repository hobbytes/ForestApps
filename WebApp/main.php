<?
/* Web App */

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$Folder = $_GET['destination'];

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'WebApp';
$AppContainer->SecondNameInfo = 'WebApp';
$AppContainer->VersionInfo = '0.1';
$AppContainer->AuthorInfo = 'Forest Media';

/* Container Info */
$AppContainer->appName = $AppName;
$AppContainer->appID = $AppID;
$AppContainer->LibraryArray = array('gui', 'permissions', 'filesystem');
$AppContainer->height = '50vh';
$AppContainer->width = '100%';
$AppContainer->customStyle = 'padding-top:0px;';
$AppContainer->StartContainer();


$faction = new fileaction;
$newpermission = new PermissionRequest;
$object = new gui;
$newpermission->fileassociate(array('webapp', 'json'), $Folder.'main.php', 'webapploader', $AppName);

$Folder = $_GET['destination'];

$user = $_SESSION['loginuser'];

$file = NULL;

$lang  = parse_ini_file('lang/main.lang');
$cl = $_SESSION['locale'];

if(isset($_GET['defaultloader'])){
	$file = $_GET['defaultloader'];
	$json = json_decode(file_get_contents($file), true);
}

if(isset($_GET['foxloader'])){
	$file = $_GET['foxloader'];

	$json = json_decode(file_get_contents('http://'.$_SERVER['SERVER_NAME'].$file), true);
}

//echo array_shift($json['icons']);



if(!empty($file)){

	if(!preg_match('%manifest.json%',$file)){
		$launch_path = $json['launch_path'];
		$file = str_replace('manifest.webapp', '', $file).$launch_path;
		$file = $faction->filehash($_SERVER['DOCUMENT_ROOT'].$file, 'false');
		$file = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
	}else{
		$file = str_replace('manifest.json', '', $file);
		foreach (glob($file.'*.html') as $filenames)
    {
			$file = $filenames;
		}
		$file = $faction->filehash($file, 'false');
		$file = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
	}

	$dir = 'http://'.$_SERVER['SERVER_NAME'].$file;

	echo '<iframe id="frame'.$AppID.'" class="app-cointainer'.$AppID.'" style="border: 0; width: inherit; height: inherit; display:block;" src="'.$dir.'"></iframe>';
}else{
	echo '<div style="width:600px; padding:10px; margin:0 auto;">'.$lang[$cl.'_help'].'</div>';
}

$AppContainer->EndContainer();
?>
<script>
/*--------JS Logic--------*/
$('#frame<? echo $AppID ?>').find('html').css('height','100%');

</script>
