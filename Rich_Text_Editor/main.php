<?
/*--------Получаем App Name и App ID--------*/
if($_GET['getinfo'] == 'true'){
	include '../../core/library/etc/appinfo.php';
	$appinfo = new AppInfo;
	$appinfo->setInfo('Rich Text Editor', '1.2', 'codepen.io', 'Rich Text Editor');
}
$appname  = $_GET['appname'];
$appid  = $_GET['appid'];
$rteloader = str_replace($_SERVER['DOCUMENT_ROOT'],'',$_GET['defaultloader']);
$mode = '1';
if(empty($rteloader)){
  $rteloader  = $_GET['rteloader'];
  $mode = '0';
}
?>
<div id="<?echo $appname.$appid;?>" style="background-color:#f2f2f2; padding-top:10px; border-radius:0px 0px 5px 5px; overflow:hidden;">
<link rel='stylesheet prefetch' href='https://netdna.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css'>

<?php
/*--------Подключаем библиотеки--------*/
include '../../core/library/etc/security.php';
include '../../core/library/permissions.php';
include '../../core/library/gui.php';
/*--------Проверяем безопасность--------*/
$security	=	new security;
$security->appprepare();
/*
Инициализируем переменные
$click - переменная используется для определения действия (клик или прикосновение)
$folder - переменная хранит место запуска программы
*/
$click=$_GET['mobile'];
$folder=$_GET['destination'];

/*--------Делаем запрос--------*/
$newpermission = new PermissionRequest;
$object = new gui;
$newpermission->fileassociate(array('txt','md'), $folder.'main.php', 'rteloader', $appname);
/*--------Запускаем сессию--------*/
session_start();
/*--------Логика--------*/
?>
<style>
.editor {
  box-shadow: 0 0 2px #CCC;
  min-height: 150px;
  overflow: auto;
  padding: 1em;
  margin: 20px 10px;
  resize: vertical;
  outline: none;
  background: #fff;
  white-space: pre-wrap;
}

.toolbar {
  text-align: center;
}

.toolbar a,
.fore-wrapper,
.back-wrapper {
  border: 1px solid #AAA;
  background: #FFF;
  font-family: 'Candal';
  border-radius: 1px;
  color: black;
  padding: 5px;
  width: 1.5em;
  margin: -2px;
  margin-top: 10px;
  display: inline-block;
  text-decoration: none;
  box-shadow: 0px 1px 0px #CCC;
}

.toolbar a:hover, .fore-wrapper:hover, .back-wrapper:hover {
  background: #f2f2f2;
  border-color: #8c8c8c;
}

a[data-command='redo'],a[data-command='strikeThrough'],a[data-command='justifyFull'],a[data-command='insertOrderedList'],a[data-command='outdent'],a[data-command='p'],a[data-command='superscript']{
  margin-right:5px;
  border-radius:0 3px 3px 0;
}

a[data-command='undo'],.fore-wrapper,a[data-command='justifyLeft'],a[data-command='insertUnorderedList'],a[data-command='indent'],a[data-command='h1'],a[data-command='subscript']{
  border-radius:3px 0 0 3px;
}

a.palette-item {
  height: 1em;
  border-radius: 3px;
  margin: 2px 1px;
  width: 1em;
}

.fore-palette,
.back-palette {
  display: none;
}

.fore-wrapper,
.back-wrapper {
  display: inline-block;
  cursor: pointer;
}

.fore-wrapper:hover .fore-palette,
.back-wrapper:hover .back-palette {
  display: block;
  float: left;
  position: absolute;
  padding: 3px;
  width: 10em;
  background: #FFF;
  border: 1px solid #DDD;
  box-shadow: 0 0 5px #CCC;
  height: 4.4em;
}

.fore-wrapper:hover .fore-palette {
  left: 34%;
  top: 42px;
}

.back-wrapper:hover .back-palette {
  left: 36%;
  top: 42px;
}

.fore-palette a,
.back-palette a {
  background: #FFF;
  margin-bottom: 2px;
}
</style>
<link href='https://fonts.googleapis.com/css?family=Dosis|Candal' rel='stylesheet' type='text/css'>
<div>
<div class="toolbar">
<a href="#" data-command='undo'><i class='fa fa-undo'></i></a>
<a href="#" data-command='redo'><i class='fa fa-repeat'></i></a>
<div class="fore-wrapper"><i class='fa fa-font' style='color:#C96;'></i>
  <div class="fore-palette" style="top:80px; left:30px;">
  </div>
</div>
<div class="back-wrapper"><i class='fa fa-font' style='background:#C96;'></i>
  <div class="back-palette" style="top:80px; left:60px;">
  </div>
</div>
<a href="#" data-command='bold'><i class='fa fa-bold'></i></a>
<a href="#" data-command='italic'><i class='fa fa-italic'></i></a>
<a href="#" data-command='underline'><i class='fa fa-underline'></i></a>
<a href="#" data-command='strikeThrough'><i class='fa fa-strikethrough'></i></a>
<a href="#" data-command='justifyLeft'><i class='fa fa-align-left'></i></a>
<a href="#" data-command='justifyCenter'><i class='fa fa-align-center'></i></a>
<a href="#" data-command='justifyRight'><i class='fa fa-align-right'></i></a>
<a href="#" data-command='justifyFull'><i class='fa fa-align-justify'></i></a>
<a href="#" data-command='indent'><i class='fa fa-indent'></i></a>
<a href="#" data-command='outdent'><i class='fa fa-outdent'></i></a>
<a href="#" data-command='insertUnorderedList'><i class='fa fa-list-ul'></i></a>
<a href="#" data-command='insertOrderedList'><i class='fa fa-list-ol'></i></a>
<a href="#" data-command='h1'>H1</a>
<a href="#" data-command='h2'>H2</a>
<a href="#" data-command='createlink'><i class='fa fa-link'></i></a>
<a href="#" data-command='unlink'><i class='fa fa-unlink'></i></a>
<a href="#" data-command='insertimage'><i class='fa fa-image'></i></a>
<a href="#" data-command='p'>P</a>
<a href="#" data-command='subscript'><i class='fa fa-subscript'></i></a>
<a href="#" data-command='superscript'><i class='fa fa-superscript'></i></a>
<div onClick='save<?echo $appid;?>()' style="width: auto;" class="ui-forest-button ui-forest-accept ui-forest-center"><span>Save</span></div>
</div>
<div id='editor<?echo $appid;?>' class="editor" contenteditable>
<?
if(isset($rteloader)){
  if($mode == '1'){
  echo htmlentities(file_get_contents($_SERVER['DOCUMENT_ROOT'].$rteloader));
}else{
  echo file_get_contents($_SERVER['DOCUMENT_ROOT'].$rteloader);
}
}
?>
</div>
</div>
</div>
<script>
/*--------Логика JS--------*/
var colorPalette = ['000000', 'FF9966', '6699FF', '99FF66', 'CC0000', '00CC00', '0000CC', '333333', '0066FF', 'FFFFFF'];
var forePalette = $('.fore-palette');
var backPalette = $('.back-palette');

for (var i = 0; i < colorPalette.length; i++) {
  forePalette.append('<a href="#" data-command="forecolor" data-value="' + '#' + colorPalette[i] + '" style="background-color:' + '#' + colorPalette[i] + ';" class="palette-item"></a>');
  backPalette.append('<a href="#" data-command="backcolor" data-value="' + '#' + colorPalette[i] + '" style="background-color:' + '#' + colorPalette[i] + ';" class="palette-item"></a>');
}

$('.toolbar a').click(function(e) {
  var command = $(this).data('command');
  if (command == 'h1' || command == 'h2' || command == 'p') {
    document.execCommand('formatBlock', false, command);
  }
  if (command == 'forecolor' || command == 'backcolor') {
    document.execCommand($(this).data('command'), false, $(this).data('value'));
  }
    if (command == 'createlink' || command == 'insertimage') {
  url = prompt('Enter the link here: ','http:\/\/'); document.execCommand($(this).data('command'), false, url);
  }
  else document.execCommand($(this).data('command'), false, null);
});

function save<?echo $appid;?>(){
  var folder = "<? echo $rteloader ?>";
  if(folder != null){
    <?
    if($mode == '0'){
    ?>
    var str = $("#editor<?echo $appid;?>").html();
    <?
  }
    else{
    ?>
    var str = $("#editor<?echo $appid;?>").text();
    <?
    }
    ?>
    $.ajax({
      type: "POST",
      url: "<?echo $folder;?>savecontent",
      data: {
         content: str,
         folder: "<?echo $_SERVER['DOCUMENT_ROOT']?>"+folder
      }
    }).done(function(o) {
  });
  }
}

UpdateWindow("<?echo $appid?>","<?echo $appname?>");
</script>
<?
unset($appid);//Очищаем переменную $appid
?>
