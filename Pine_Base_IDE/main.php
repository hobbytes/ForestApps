<?$appname=$_GET['appname'];$appid=$_GET['appid'];?>
<div id="<?echo $appname.$appid;?>" style="background-color:#1b1b1b; height:700px; max-height:95%; max-width:100%; width:800px; color:#f2f2f2; border-radius:0px 0px 5px 5px; overflow:hidden;">
<?php
/*Application Store*/
//Подключаем библиотеки
//Инициализируем переменные
$click=$_GET['mobile'];
$launch=$_GET['launch'];
$_GET['content'];
$savecon=preg_replace('#%u([0-9A-F]{4})#se','iconv("UTF-16BE","UTF-8",pack("H4","$1"))',$_GET['content']);
$folder=$_GET['destination'];
//Запускаем сессию
session_start();
//Логика
if($launch=='true'){
  if(!is_dir('./temp')){
    mkdir('./temp');
  }
  $name='temp_'.date('dmyH');
  $tempfile=$name;
  $myfile=fopen('temp/'.$tempfile,"w");
  fwrite($myfile,$savecon);
  fclose($myfile);
  ?>
  <script>makeprocess2('<?echo $folder.'temp/'.$tempfile.'&destination='.$folder.'temp/'?>','','');</script>
  <?
}
?>
<link rel="stylesheet" href="<?echo $folder;?>assets/highlight/styles/atom-one-dark.css">
<div style="width:100%; text-align:center; margin:0 auto; background-color:#292929; padding:10px;">
  <div style="cursor:pointer; width:30px; text-align:left; " onmouseover="document.getElementById('filemenu<?echo $appid;?>').style.display='block';" onmouseout="document.getElementById('filemenu<?echo $appid;?>').style.display='none';">
  	Файл
  	<div id="filemenu<?echo $appid;?>" style="display:none; position:absolute; z-index:9000; background:#fff; width:auto;">
  <ul id="mmenu<?echo $appid;?>">
  	<li><div>Создать проект</div></li>
    	<li><div>Создать файл</div></li>
  	     <li><div>Сохранить</div></li>
  	       <li><div>Сохранить как...</div></li>
  </ul>
  </div>
  </div>
  <div id="launchapp" onClick="launch<?echo $appid;?>();" class="ui-button ui-widget ui-corner-all"><span class="ui-icon ui-icon-play">Run</span></div>
    <div class="ui-button ui-widget ui-corner-all"><span class="ui-icon ui-icon-stop">Stop</span></div>
</div>
<table style="height:100%; display:block; color:#f2f2f2; border-spacing: 0;">
  <tr>
    <td>
      Проводник
    </td>
    <td style="height:500px; width:97%; display:block;">
        <div class="hljs" onchange="hlupd<?echo $appid;?>()" contenteditable="true" style="height:100%; display:block; border: 1px solid #3c3c3c;">
          <code>
            <div style="white-space:pre-wrap;" id="content<?echo $appid;?>">
          <?
          if($launch=='true'){
          $handle=fopen("temp/$tempfile","r+");
          $contents='';
          while(!feof($handle)){
          $contents=htmlentities(fgets($handle));
          echo $contents;
        }
          fclose($handle);
          }
          else
          {
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
<script>
function launch<?echo $appid;?>(){
  var str = $("#content<?echo $appid;?>").text();
  str = str.replace(/<span class="hljs.*">([\s\S]+?)<\/span>/gim, "$i");
  //alert(str);
  $("#<?echo $appid;?>").load("<?echo $folder;?>main.php?launch=true&content="+escape(str)+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&destination='.$folder;?>")
};

$(function(){
  $("#mmenu<?echo $appid;?>").menu();
});

function hlupd<?echo $appid;?>(){
  $(document).ready(function()  {
    $('div code').each(function(i, block){
      hljs.highlightBlock(block);
    });
  });
};

$.getScript('<?echo $folder;?>assets/highlight/highlight.pack.js')
  .done(function( script, textStatus  ){
    hlupd<?echo $appid;?>();
    console.log('highlight.js is load');
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
