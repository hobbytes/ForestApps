<div id="DagomysWeather" style="background: url('https://dagomysweather.ru/source/img/walls/wall.jpg?ran=<?echo rand(0,1000);?>') 100% 100% / cover no-repeat fixed;background-color:#f2f2f2; height:100%; width:100%; padding-top:10px; border-radius:0px 0px 5px 5px; overflow:auto;">
<link rel="stylesheet" type="text/css" href="system/apps/Dagomys_Weather/assets/style.css">
<?php
/*Test*/
//Подключаем библиотеки
include '../../core/library/etc/security.php';
$security	=	new security;
$security->appprepare();
//Инициализируем переменные
$click=$_GET['mobile'];
//Запускаем сессию
session_start();
//обрабатываем кновку удаления
$appid=$_GET['appid'];
//Логика
$file=file_get_contents('https://dagomysweather.ru/source/scripts/temperature/main.php');
echo $file;
?>
</div>
<script>
</script>
<?
unset($appid);
?>
