<?
if($_GET['getinfo'] == 'true'){
	include '../../core/library/etc/appinfo.php';
	$appinfo = new AppInfo;
	$appinfo->setInfo('Console', '1.1', 'Forest Media', 'Консоль');
}
$appname=$_GET['appname'];
$appid=$_GET['appid'];
?>
<div id="<?echo $appname.$appid;?>" style="background-color:#1b1b1b; height:100%; color:#f2f2f2; width:100%; border-radius:0px 0px 5px 5px; overflow:hidden;">
<?php
/*Console*/
//Подключаем библиотеки
include '../../core/library/etc/security.php';
$security	=	new security;
$security->appprepare();
//Инициализируем переменные
$click=$_GET['mobile'];
$appdownload=$_GET['appdownload'];
$type=$_GET['type'];
$folder=$_GET['destination'];
if($_SESSION['loginuser'] == $_SESSION['superuser']){
  $text=preg_replace('#%u([0-9A-F]{4})#se','iconv("UTF-16BE","UTF-8",pack("H4","$1"))',$_GET["command"]);
}
//Логика
?>
<textarea cols="50" rows="10" style="background-color: #1e1f29; color: #50fb6f; width:100%; margin:auto; border: none; padding: 10px;" id="command<?echo $appid;?>"></textarea>
<?
if($_SESSION['loginuser'] == $_SESSION['superuser']){
?>
<div id="launchapp" style="display:block; margin:auto;" onClick="launch<?echo $appid;?>(this);" class="ui-button ui-widget ui-corner-all">Выполнить</div>
<?}?>
<div style="background-color:#1e1f29; color:#fff; word-wrap:break-word; width:400px; padding:10px; margin:auto; text-align:left;">
<?
echo 'Команда: <i style="color:grey;">'.$text.'</i><br>Ответ: ';
if($_SESSION['loginuser'] == $_SESSION['superuser']){
try {
  eval ($text);
} catch (Exception $e) {
  echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
} echo "\n\n\n";
}else{
  echo '#error: you are not superuser!';
}
?>
</div>
</div>
<script>
function launch<?echo $appid;?>(el){$("#<?echo $appid;?>").load("<?echo $folder;?>main.php?command="+escape($("#command<?echo $appid;?>").val())+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&destination='.$folder;?>")};
</script>
<?
unset($appid);
?>
