<?
/* Pine Base IDE */
$AppName  = $_GET['appname'];
$AppID  = $_GET['appid'];

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'Pine Base IDE';
$AppContainer->SecondNameInfo = 'Pine Base IDE';
$AppContainer->VersionInfo = '1.7';
$AppContainer->AuthorInfo = 'Forest Media';

/* Library List */
$AppContainer->LibraryArray = Array('permissions','gui');

/* Container Info */
$AppContainer->appName = $AppName;
$AppContainer->appID = $AppID;
$AppContainer->backgroundColor = '#1b1b1b';
$AppContainer->fontColor = '#f2f2f2';
$AppContainer->height = '700px';
$AppContainer->width = '800px';
$AppContainer->customStyle = 'overflow-y:auto; overflow-x:hidden; padding-top:0px;';
$AppContainer->StartContainer();

/*include third-party lib*/
include 'assets/libs/phpFileTree/php_file_tree.php';
$object = new gui;
$newpermission = new PermissionRequest;

//Инициализируем переменные
$isMobile  = $_GET['mobile'];
$launch = $_GET['launch'];
$Folder = $_GET['destination'];
$filedir  = $_GET['filedir'];

$openexplorer = str_replace($_SERVER['DOCUMENT_ROOT'],'',$_GET['defaultloader']);
if(empty($openexplorer)){
  $openexplorer  = $_GET['pbloader'];
}

$savecon  = preg_replace('#%u([0-9A-F]{4})#se','iconv("UTF-16BE","UTF-8",pack("H4","$1"))',$_GET['content']);

/*****Ассоциируем файлы*****/
$newpermission->fileassociate(array('foc','js','css','ini','fth','link'), $Folder.'main.php', 'pbloader', $AppName);
//Запускаем сессию
session_start();
//Логика
if($launch  ==  'true'){
  if(!isset($filedir)){
    if(!is_dir('./temp')){
      mkdir('./temp');
    }
    $filedir  = 'temp/';
    $tempfile = 'temp_'.date('dmyH').'.php';
    $myfile=fopen($filedir.$tempfile,"w");
    fwrite($myfile,$savecon);
    fclose($myfile);
}else{
}
  ?>
  <script>makeprocess('<?echo $Folder.$filedir.$tempfile.'&destination='.$Folder.'temp/'?>','','','<?echo $tempfile?>');</script>
  <?
  $inputdir = $Folder.$filedir.$tempfile;
}
else if($launch  ===  'open' || isset($filedir)){
  $tempfile = $filedir;
  $inputdir = $filedir;
}
else if(isset($openexplorer)){
  $tempfile = $_SERVER['DOCUMENT_ROOT'].$openexplorer;
  $inputdir = $_SERVER['DOCUMENT_ROOT'].$openexplorer;
}else{
  $tempfile = 'template.php';
  $inputdir = $tempfile;
}

?>
<link rel="stylesheet" href="<? echo $Folder.'assets/libs/phpFileTree/styles/default/default.css?h='.md5(date('dmyhis'))?>">
<link rel="stylesheet" href="<?echo $Folder;?>assets/highlight/styles/atom-one-dark.css">
<div style="width:98%; text-align:center; margin:0 auto; background-color:#292929; padding:10px;">
  <div style="display:none; cursor:pointer; width:30px; text-align:left; " onmouseover="document.getElementById('filemenu<?echo $AppID;?>').style.display='block';" onmouseout="document.getElementById('filemenu<?echo $AppID;?>').style.display='none';">
  	Файл
  	<div id="filemenu<?echo $AppID;?>" style="display:none; position:absolute; z-index:9000; background:#fff; width:auto;">
  <ul id="mmenu<?echo $AppID;?>">
    <li><div>Open file</div></li>
    <li><div>Create file</div></li>
  	<li><div onclick="savefile<?echo $AppID;?>($('#destionation<?echo $AppID;?>.value()'))">Save</div></li>
  	<li><div>Save as..</div></li>
  </ul>
  </div>
  </div>
  <div id="launchapp" onClick="launch<?echo $AppID;?>()" class="ui-button ui-widget ui-corner-all">
    <span class="ui-icon ui-icon-play"></span>Run
  </div>
  <div class="ui-button ui-widget ui-corner-all">
    <span class="ui-icon ui-icon-stop"></span>Stop
  </div>
  <div id="launchapp" onClick="savefile<?echo $AppID;?>('<?echo $inputdir;?>')" class="ui-button ui-widget ui-corner-all">
    <span class="ui-icon ui-icon-disk"></span>Save
  </div>
  <div>
    <input style="background-color:#403f3f; border:1px solid #a5a5a5;  font-size:15px; color:#fff; padding:5px;  width:70%;  margin:10px;" type="text" id="destionation<?echo $AppID;?>" value="<?echo $inputdir;?>">
  </div>
</div>
<div style="display:grid; grid-template-columns: 17% 81%; height:100%; color:#f2f2f2; border-spacing: 0;">
    <div id="dirtree" style="display:block; float:left;">
      <?
      $allowed_extensions = array("php", "js", "ini", "css", "foc");
      echo php_file_tree($_SERVER['DOCUMENT_ROOT'], "javascript:loadfile$AppID('[link]')",  $allowed_extensions);
      ?>
    </div>
    <div id="contentget<?echo $AppID;?>" style="height:500px; display:block;">
        <div class="hljs" onchange="hlupd<?echo $AppID;?>()" contenteditable="true" style="display:block; border: 1px solid #3c3c3c;">
          <code>
            <div style="white-space:pre-wrap; max-width:800px; min-width:600px;" id="content<?echo $AppID;?>"><?
          if($launch=='true'){
              $handle=fopen($filedir.$tempfile,"r+");
              $contents='';
              while(!feof($handle)){
                $contents=htmlentities(fgets($handle));
                echo $contents;
            }
              fclose($handle);
          }
          elseif($launch  === 'open' || isset($filedir)){
            $handle=fopen($filedir,"r+");
            $contents='';
            while(!feof($handle)){
              $contents=htmlentities(fgets($handle));
              echo $contents;
          }
            fclose($handle);
          }
          elseif(isset($openexplorer)){
            $handle=fopen($_SERVER['DOCUMENT_ROOT'].$openexplorer,"r+");
            $contents='';
            while(!feof($handle)){
              $contents=htmlentities(fgets($handle));
              echo $contents;
          }
            fclose($handle);
          }else{
            $content = file_get_contents('template.php');
            $content = htmlentities($content);
            echo $content;
          }
          ?>
        </div>
          </code>
            </div>
</div>
</div>
<?
$AppContainer->EndContainer();
?>
<script>
function launch<?echo $AppID?>(){
  var str = $("#contentget<?echo $AppID?>").text();
  //str = str.replace(/<span class="hljs.*">([\s\S]+?)<\/span>/gim, "$i");
  //str = str.replace(/^\s*/,'').replace(/\s*$/,'');
  console.log(str);
  //$("#<?echo $AppID?>").load("<?echo $Folder?>main.php?launch=true&content="+encodeURIComponent(str)+"&id=<?echo rand(0,10000).'&appid='.$AppID.'&mobile='.$isMobile.'&appname='.$AppName.'&destination='.$Folder;?>")
};

function loadfile<?echo $AppID?>(file){
  $("#<?echo $AppID?>").load("<?echo $Folder?>main.php?launch=open&filedir="+file+"&id=<?echo rand(0,10000).'&appid='.$AppID.'&mobile='.$isMobile.'&appname='.$AppName.'&destination='.$Folder;?>")
}

function savefile<?echo $AppID;?>(destination){
  var str = $("#contentget<?echo $AppID?>").text();
  str = str.replace(/<span class="hljs.*">([\s\S]+?)<\/span>/gim, "$i");
  //str = str.replace(/^\s*/,'').replace(/\s*$/,'');
  $.ajax({
    type: "POST",
    url: "<?echo $Folder?>savecontent",
    data: {
       content:str,
       folder:destination
    }
  }).done(function(o) {
//console.log('saved');
});
}

$(function(){
  //$("#mmenu<?echo $AppID;?>").menu();
});

function hlupd<?echo $AppID;?>(){
  $(document).ready(function()  {
    var str = $("#content<?echo $AppID;?>").text();
    //str = str.replace(/^\s*/,'').replace(/\s*$/,'');
    //$("#content<?echo $AppID;?>").text(str);
    $('div code').each(function(i, block){
      //hljs.highlightBlock(block);
    });
    $( "#content<?echo $AppID;?>" ).resizable({containment:"body",autoHide:true});
  });
};

$(document).ready(function()  {
$.getScript('<?echo $Folder;?>assets/highlight/highlight.pack.js')
  .done(function( script, textStatus  ){
    hlupd<?echo $AppID;?>();
    //console.log('highlight.js is load');
  });

  $.getScript('<?echo $Folder;?>assets/libs/phpFileTree/php_file_tree.js')
    .done(function( script, textStatus  ){
      //console.log('php_file_tree.js is load');
    });
  });

</script>
<style>
.ui-menu{
  width: 150px;
  background-color:#1b1b1b;
  color:#f2f2f2;
  font-size: 13px;
  }
</style>
<?
unset($AppID);
?>
