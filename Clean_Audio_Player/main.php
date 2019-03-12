<?

/*--------Get App Name and App ID--------*/

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$Folder = $_GET['destination'];

/*--------Require Mercury library--------*/
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'Clean Audio Player'; // app name information @string
$AppContainer->SecondNameInfo = 'Clean Audio Player'; // second app name information @string
$AppContainer->VersionInfo = '1.0';  // app version @string
$AppContainer->AuthorInfo = 'Forest Media'; // app version @string

/* Library List */
$AppContainer->LibraryArray = array('gui', 'permissions');

/* Container Info */
$AppContainer->appName = $AppName;
$AppContainer->appID = $AppID;
$AppContainer->height = 'max-content;';
$AppContainer->width = '100%';

/* start app container */
$AppContainer->StartContainer();

$musicloader = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_GET['defaultloader']);

if(empty($musicloader)){
  $musicloader  = $_GET['musicloader'];
}

$autoplay = 'autoplay';

if(empty($musicloader)){
  $autoplay = 'false';
}

$NewPermission = new PermissionRequest;
$object = new gui;
$NewPermission->fileassociate(array('mp3', 'wav', 'ogg', 'aac'), $Folder.'main.php', 'musicloader', $AppName);

?>
<link rel="stylesheet" href="<? echo $Folder ?>/css/audioplayer.css?h=1" />

<style>
<?echo "#wrapper$AppID" ?> { margin: 10px auto; max-width: 400px; width: 400px; height: 100px;}
</style>

<div id="<?echo "wrapper$AppID" ?>">
  <div style="text-align: center; color: #272727; font-size: 20px; word-break: break-word;">
    <? echo basename(str_replace('_', ' ', $musicloader)) ?>
  </div>
  <audio id="<?echo "audio$AppID" ?>" preload="auto" autoplay="<? echo $autoplay ?>" controls>
    <source src="<? echo $musicloader ?>">
  </audio>
</div>
<?


/* end app container */
$AppContainer->EndContainer();

?>

<script>

  $.getScript('<? echo $Folder ?>/js/audioplayer.js')
  .done(function( script, textStatus  ){
    $('<?echo "#audio$AppID" ?>').audioPlayer();
  });

</script>
