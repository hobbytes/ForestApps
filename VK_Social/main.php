<?
/*--------Получаем App Name и App ID--------*/
$appname=$_GET['appname'];
$appid=$_GET['appid'];
?>
<div id="<?echo $appname.$appid;?>" style="background-color:#e2e2e2; height:100%; width:100%; border-radius:0px 0px 5px 5px; overflow:auto;">
<?php
/*--------Подключаем библиотеки--------*/
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/etc/security.php';
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/bd.php';
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/gui.php';
//$language  = parse_ini_file('app.lang');
/*--------Запускаем сессию--------*/
session_start();
/*--------Проверяем безопасность--------*/
$security	=	new security;
$security->appprepare();
$click=$_GET['mobile'];
$folder=$_GET['destination'];
$settingsbd = new readbd;
$gui = new gui;

/*--------Логика--------*/

$settingsbd->readglobal2("fuid","forestusers","login",$_SESSION["loginuser"]);
$fuid = $getdata;
$settingsbd->readglobal2("password","forestusers","login",$_SESSION["loginuser"]);
$password = $getdata;
$d_root = $_SERVER['DOCUMENT_ROOT'];
$token = md5($fuid.$d_root.$password);
$hash = md5(date('d.m.y h:i:s'));
$get_domain = $_GET['domain'];

$get_json = file_get_contents('http://forest.hobbytes.com/media/os/modules/social.php?token='.$token.'&id='.$get_domain.'&h='.$hash);
$json = json_decode($get_json,TRUE);
?>
<style>
.s-container{
  background-color: #fff;
  border-bottom: 1px solid #ddd;
  padding:  10px;
}
.name-container{
  color: #8e8e8e;
}
</style>
<script>
function add_domain<?echo $appid?>(){
  $("#<?echo $appid?>").load("<?echo $folder?>main.php?id=<?echo rand(0,10000).'&destination='.$folder.'&appname='.$appname.'&appid='.$appid?>&domain="+$('#<?echo $appid?>domain').val());
}
</script>
<div style="width:100%; height:auto">
  <?
    if($json['online']  ==  0){
      $online = 'не в сети - '.$json['last_seen'];
    }elseif($json['online']  ==  1){
      $online = 'онлайн';
    }
    echo '<div style="padding:10px; background-color:#5f86c4; color:#fff;">
  '.$json['first_name'].' '.$json['last_name'].'
  <div style="color:#d6d6d6; font-size:13px;">'.$online.'</div>
  <div style="text-align:right;">
  <input id="'.$appid.'domain" style="border:1px solid #ccc; font-size:20px; border-radius:5px; width: 150px; padding: 5px; margin: 0 10;" value="'.$get_domain.'" type="text" name="'.$appid.'domain">
  <div onClick="add_domain'.$appid.'()" class="ui-forest-button ui-forest-accept" style="float:right;">Analyze</div>
  </div>
  </div>';

  if(!empty($json['error'])){
    die($gui->errorLayot($json['error']));
  }else{
    function dataContainer($name, $data){
      if(!empty($data)){
        echo '<div class="s-container"><div class="name-container">'.$name.'</div>'.$data.'</div>';
      }
    }
    echo '<div style="margin:10px 0;">';
    echo '<img src="'.$json['small_photo'].'" style="padding:10px; border-radius:60px; width:100px; height:100px;" class="ui-forest-blink" onClick="makeprocess(\'system/apps/Image_Viewer/main.php\',\''.$json['large_photo'].'\',\'photoviewload\', \'Image_Viewer\')">';
    dataContainer('id', $json['id'].' | '.$json['domain']);
    dataContainer('Город', $json['country'].', '.$json['home_town']);
    dataContainer('Интересы', $json['interests']);
    echo '</div>';
    echo '<div style="margin:10px 0;">';
    dataContainer('Номер телефона', $json['mobile_phone']);
    dataContainer('Twitter', $json['twitter']);
    dataContainer('Facebook', $json['facebook']);
    dataContainer('Instagram', $json['instagram']);
    echo '</div>';
    echo '<div style="margin:10px 0;">';
    dataContainer('Ключевые слова', $json['keywords']);
    echo '</div>';
    echo '<div class="s-container"><div class="name-container">Возможные родственники</div>';
    $i=0;
    foreach ($json['family'] as $key)
    {
      for ($i = 0; $i < count($key); $i++) {
        if(!empty($key[$i]['uid'])){
          echo '<div>'.$key[$i]['first_name'].' '.$key[$i]['last_name'].'<span style="color:#fff; font-size:18px; background-color:#5f86c4; cursor:pointer; padding: 0 10px; border-radius:5px; margin:auto 5px;" onClick="makeprocess(\'system/apps/VK_Social/main.php\',\''.$key[$i]['uid'].'\',\'domain\', \''.$appname.'\')"> analyze </span></div><br>';
        }
      }
    }
    echo '</div>';
  }

  ?>
</div>

</div>
<script>
UpdateWindow("<?echo $appid?>","<?echo $appname?>");
</script>
<?
unset($appid);
?>
