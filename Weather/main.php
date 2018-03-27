<?
/*--------Получаем App Name и App ID--------*/
if($_GET['getinfo'] == 'true'){
	include '../../core/library/etc/appinfo.php';
	$appinfo = new AppInfo;
	$appinfo->setInfo('Weather', '1.0', 'Forest Media', 'Погода');
}
$appname=$_GET['appname'];
$appid=$_GET['appid'];
?>
<div id="<?echo $appname.$appid;?>" style="background: url('https://picsum.photos/900/700/?&h=<?echo md5(date('dmyh'))?>') 100% 100% / cover no-repeat fixed; background-color:#f2f2f2; height:100%; width:100%; border-radius:0px 0px 5px 5px;">
<?php
/*--------Подключаем библиотеки--------*/
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/etc/security.php';
/*--------Запускаем сессию--------*/
session_start();
/*--------Проверяем безопасность--------*/
$security	=	new security;
$security->appprepare();

$click	=	$_GET['mobile'];
$folder	=	$_GET['destination'];
/*--------Логика--------*/
$dir = $_SERVER['DOCUMENT_ROOT'].'/system/users/'.$_SESSION['loginuser'].'/documents/Weather/';
if(!is_dir($dir)){
	mkdir($dir);
}
if(!empty($_GET['newcity'])){
	file_put_contents($dir.'data.foc',$_GET['newcity']);
}
$getcity = file_get_contents($dir.'data.foc');
if(empty($getcity)){
	$getcity = 'Moscow';
}

$json = file_get_contents('http://api.openweathermap.org/data/2.5/weather?&appid=98ba4333281c6d0711ca78d2d0481c3d&units=metric&cnt=17&q='.$getcity.'&lang=ru');
$array = json_decode($json);
$temp = $array->main->temp;
if ($temp	>	0){
	$temp='+'.$temp;
}
$pressure = $array->main->pressure/1.33322387415;
$humidity = $array->main->humidity;
$speed = $array->wind->speed;
$description = $array->weather[0]->description;
$name = $array->name;
$icon = 'http://openweathermap.org/img/w/'.$array->weather[0]->icon.'.png';
echo '<div style="font-size:20px; color:#fff; padding:10px 0px; background-color:rgba(0,0,0,0.65); width:100%; height:100%; text-align:center;">';
echo $name.', '.date('d.m.y');
echo '<div style="font-size:70px; font-weight:600; padding: 0 47px; text-shadow:1px 0px 5px #000;">'.$temp.'<sup>o</sup>C</div>';
echo '<div>Сейчас '.$description.'<img style="vertical-align:middle;" src="'.$icon.'"/></div>';
echo '<div>Давление: '.round($pressure).' мм. рт.ст.</div>';
echo '<div>Влажность: '.$humidity.'%</div>';
echo '<div>Скорость ветра: '.$speed.' м/с</div>';
echo '<input id="cityname'.$appid.'" type="text" value="'.$getcity.'" style="background:none; border:2px solid #fff; font-size:18px; text-align:center; margin:10 10 0; color:#fff;">';
echo '<div onClick="changecity'.$appid.'();" style="margin:10px auto;" class="ui-forest-button ui-forest-twhite">выбрать</div>';
echo '</div>';
?>
</div>
<script>
/*--------Логика JS--------*/
function changecity<?echo $appid;?>(el){$("#<?echo $appid;?>").load("<?echo $folder;?>/main.php?newcity="+$("#cityname<?echo $appid;?>").val()+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&destination='.$folder;?>")};
</script>
<?
unset($appid);//Очищаем переменную $appid
?>