<?
/*--------Get App Name and App ID--------*/
if($_GET['getinfo'] == 'true'){
	include '../../core/library/etc/appinfo.php';
	$appinfo = new AppInfo;
	$appinfo->setInfo('App Name', '1.0', 'Author', 'Second Name');
}
$appname=$_GET['appname'];
$appid=$_GET['appid'];
?>
<div id="<?echo $appname.$appid;?>" style="background-color:#f2f2f2; height:100%; width:100%; border-radius:0px 0px 5px 5px; overflow:auto;">
<?php
/*--------Include Libraries--------*/
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/etc/security.php';
/*--------Run Session--------*/
session_start();
/*--------Check Security--------*/
$security	=	new security;
$security->appprepare();
/*
Инициализируем переменные
$click - click or touch action
$folder - folder var
*/
$click=$_GET['mobile'];
$folder=$_GET['destination'];
/*--------PHP Logic--------*/
?>
</div>
<script>
/*--------JS Logic--------*/
</script>
