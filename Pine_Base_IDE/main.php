<?
if($_GET['getinfo'] == 'true'){
	include '../../core/library/etc/appinfo.php';
	$appinfo = new AppInfo;
	$appinfo->setInfo('Pine Base IDE', '1.6', 'Forest Media', 'Pine Base IDE');
}
$appname  = $_GET['appname'];
$appid  = $_GET['appid'];
?>
<div id="<?echo $appname.$appid;?>" style="background-color:#1b1b1b; height:700px; max-height:95%; max-width:100%; width:800px; color:#f2f2f2; border-radius:0px 0px 5px 5px; overflow-y:auto; overflow-x:hidden;">
<?php
/*****Pine Base IDE*****/
/*****Подключаем библиотеки*****/
include 'assets/libs/phpFileTree/php_file_tree.php';
include '../../core/library/permissions.php';
include '../../core/library/gui.php';
include '../../core/library/etc/security.php';
$security	=	new security;
$object = new gui;
$newpermission = new PermissionRequest;
$security->appprepare();
//Инициализируем переменные
$click  = $_GET['mobile'];
$launch = $_GET['launch'];
$folder = $_GET['destination'];
$filedir  = $_GET['filedir'];

$openexplorer = str_replace($_SERVER['DOCUMENT_ROOT'],'',$_GET['defaultloader']);
if(empty($openexplorer)){
  $openexplorer  = $_GET['pbloader'];
}
$savecon  = preg_replace('#%u([0-9A-F]{4})#se','iconv("UTF-16BE","UTF-8",pack("H4","$1"))',$_GET['content']);
/*****Ассоциируем файлы*****/
$newpermission->fileassociate(array('foc','js','css','ini','fth','link'), $folder.'main.php', 'pbloader', $appname);
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
  <script>makeprocess('<?echo $folder.$filedir.$tempfile.'&destination='.$folder.'temp/'?>','','','<?echo $tempfile?>');</script>
  <?
  $inputdir = $folder.$filedir.$tempfile;
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
<link rel="stylesheet" href="<? echo $folder.'assets/libs/phpFileTree/styles/default/default.css?h='.md5(date('dmyhis'))?>">
<link rel="stylesheet" href="<?echo $folder;?>assets/highlight/styles/atom-one-dark.css">
<div style="width:98%; text-align:center; margin:0 auto; background-color:#292929; padding:10px;">
  <div style="cursor:pointer; width:30px; text-align:left; " onmouseover="document.getElementById('filemenu<?echo $appid;?>').style.display='block';" onmouseout="document.getElementById('filemenu<?echo $appid;?>').style.display='none';">
  	Файл
  	<div id="filemenu<?echo $appid;?>" style="display:none; position:absolute; z-index:9000; background:#fff; width:auto;">
  <ul id="mmenu<?echo $appid;?>">
    <li><div>Open file</div></li>
    <li><div>Create file</div></li>
  	<li><div onclick="savefile<?echo $appid;?>($('#destionation<?echo $appid;?>.value()'))">Save</div></li>
  	<li><div>Save as..</div></li>
  </ul>
  </div>
  </div>
  <div id="launchapp" onClick="launch<?echo $appid;?>()" class="ui-button ui-widget ui-corner-all">
    <span class="ui-icon ui-icon-play"></span>Run
  </div>
  <div class="ui-button ui-widget ui-corner-all">
    <span class="ui-icon ui-icon-stop"></span>Stop
  </div>
  <div id="launchapp" onClick="savefile<?echo $appid;?>('<?echo $inputdir;?>')" class="ui-button ui-widget ui-corner-all">
    <span class="ui-icon ui-icon-disk"></span>Save
  </div>
  <div>
    <input style="background-color:#403f3f; border:1px solid #a5a5a5;  font-size:15px; color:#fff; padding:5px;  width:70%;  margin:10px;" type="text" id="destionation<?echo $appid;?>" value="<?echo $inputdir;?>">
  </div>
</div>
<table style="height:100%; display:block; color:#f2f2f2; border-spacing: 0;">
  <tr>
    <td id="dirtree" style="display:block; float:left;">
      <?
      $allowed_extensions = array("php", "js", "ini", "css", "foc");
      echo php_file_tree($_SERVER['DOCUMENT_ROOT'], "javascript:loadfile$appid('[link]')",  $allowed_extensions);
      ?>
    </td>
    <td id="contentget<?echo $appid;?>" style="height:500px; width:97%; display:block;">
        <div class="hljs" onchange="hlupd<?echo $appid;?>()" contenteditable="true" style="display:block; border: 1px solid #3c3c3c;">
          <code>
            <div style="white-space:pre-wrap; max-width:800px; min-width:600px;" id="content<?echo $appid;?>">
          <?
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
</td>
</tr>
</table>
</div>
<script>
function launch<?echo $appid;?>(){
  var str = $("#contentget<?echo $appid;?>").text();
  str = str.replace(/<span class="hljs.*">([\s\S]+?)<\/span>/gim, "$i");
  str = str.replace(/^\s*/,'').replace(/\s*$/,'');
  //console.log(str);
  $("#<?echo $appid;?>").load("<?echo $folder;?>main.php?launch=true&content="+escape(str)+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&destination='.$folder;?>")
};

function loadfile<?echo $appid;?>(file){
  $("#<?echo $appid;?>").load("<?echo $folder;?>main.php?launch=open&filedir="+file+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&destination='.$folder;?>")
}

function savefile<?echo $appid;?>(destination){
  var str = $("#contentget<?echo $appid;?>").text();
  str = str.replace(/<span class="hljs.*">([\s\S]+?)<\/span>/gim, "$i");
  str = str.replace(/^\s*/,'').replace(/\s*$/,'');
  $.ajax({
    type: "POST",
    url: "<?echo $folder;?>savecontent",
    data: {
       content:str,
       folder:destination
    }
  }).done(function(o) {
//console.log('saved');
});
}

$(function(){
  $("#mmenu<?echo $appid;?>").menu();
});

function hlupd<?echo $appid;?>(){
  $(document).ready(function()  {
    var str = $("#content<?echo $appid;?>").text();
    str = str.replace(/^\s*/,'').replace(/\s*$/,'');
    $("#content<?echo $appid;?>").text(str);
    $('div code').each(function(i, block){
      hljs.highlightBlock(block);
    });
    $( "#content<?echo $appid;?>" ).resizable({containment:"body",autoHide:true});
  });
};

$(document).ready(function()  {
$.getScript('<?echo $folder;?>assets/highlight/highlight.pack.js')
  .done(function( script, textStatus  ){
    hlupd<?echo $appid;?>();
    //console.log('highlight.js is load');
  });

  $.getScript('<?echo $folder;?>assets/libs/phpFileTree/php_file_tree.js')
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
unset($appid);
?>
