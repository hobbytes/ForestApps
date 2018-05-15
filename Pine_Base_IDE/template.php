<?

/*--------Get App Name and App ID--------*/

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];

/*--------Require Mercury library--------*/
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'App name'; // app name information @string
$AppContainer->SecondNameInfo = 'Second Name'; // second app name information @string
$AppContainer->VersionInfo = '1.0';  // app version @string
$AppContainer->AuthorInfo = 'Author'; // app version @string

/* Library List */
$AppContainer->LibraryArray = Array(); // get libraries @array *not necessary

/* Container Info */
$AppContainer->appName = $AppName; // app container name @string
$AppContainer->appID = $AppID; //  app container ID @integer
$AppContainer->backgroundColor = '#f2f2f2'; // custom background-color *not necessary
$AppContainer->fontColor = '#000'; // custom font color *not necessary
$AppContainer->height = '500px';  // app container height @string *not necessary
$AppContainer->width = '800px'; // app container width @string *not necessary
$AppContainer->customStyle = ''; // custom CSS style @string *not necessary
$AppContainer->showError = false; // error display @boolean *not necessary

/* start app container */
$AppContainer->StartContainer();

/*
$isMobile - click or touch event,
$folder - application directory
*/

$isMobile = $_GET['mobile'];
$folder = $_GET['destination'];

/* print Hello World! */

echo 'Hello World!';

/* end app container */
$AppContainer->EndContainer();

?>

<script>

/*--------JS Logic--------*/

</script>
