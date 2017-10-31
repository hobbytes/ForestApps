<?
/*--------Получаем App Name и App ID--------*/
$appname=$_GET['appname'];
$appid=$_GET['appid'];
?>
<div id="<?echo $appname.$appid;?>" style="background-color:#f2f2f2; height:100%; width:100%; padding-top:10px; border-radius:0px 0px 5px 5px; overflow:auto;">
<?php
/*--------Подключаем библиотеки--------*/
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/etc/security.php';
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/gui.php';
$miner_security	=	new security;
$miner_gui = new gui;
/*--------Запускаем сессию--------*/
session_start();
/*--------Проверяем безопасность--------*/
$miner_security->appprepare();
/*
Инициализируем переменные
$click - переменная используется для определения действия (клик или прикосновение)
$folder - переменная хранит место запуска программы
*/
$click  = $_GET['mobile'];
$folder = $_GET['destination'];
$key_file_name = 'site_key.foc';
$doc_dir = '../../users/'.$_SESSION['loginuser'].'/documents';
$key_dir = 'Web_Miner';
$key_file = $doc_dir.'/'.$key_dir.'/'.$key_file_name;
if(!is_dir($doc_dir)){
  mkdir($doc_dir);
}
elseif (!is_dir($doc_dir.'/'.$key_dir)) {
  mkdir($doc_dir.'/'.$key_dir);
}
if(isset($_GET['sitekey'])){
  file_put_contents($key_file,  $_GET['sitekey']);
}
if(isset($_GET['resetkey'])){
  unlink($key_file);
}
$key = file_get_contents($key_file);
/*--------Логика--------*/
if(empty($key)){
  $miner_gui->inputslabel('', 'text', ''.$appid.'addsitekey', '','100', 'YOUR SITE KEY');
  echo '<div id="addkeybtn'.$appid.'" onClick="addkey'.$appid.'();" class="ui-forest-button ui-forest-accept ui-forest-center">START</div>';
}else{
  echo '
  <div style="width:400px; text-align:center; background: #a7d28c; padding: 10px; margin:10px; color:#2d731c; border: 2px solid;">
  Перед запуском убедитесь, что у вас отключен антивирус или этот сайт добавлен в исключения
  </div>
  <div id="error'.$appid.'" style="width:400px; text-align:center; background: #cc6565; padding: 10px; margin:10px; color:#612b2b; display:none; border: 2px solid;">
  Ошибка! Перезапустите систему и заново откройте программу.
  </div>
  <div style="padding:10px; width:400px; color: #1a4c98;">
  <div id="h_found'.$appid.'"></div>
  <div id="h_accept'.$appid.'"></div><br>
  <div id="h_ps'.$appid.'"></div>
  <div id="h_total'.$appid.'"></div>
  <div id="h_accepted'.$appid.'"></div>
  <div id="h_threads'.$appid.'"></div>
  <div id="h_throttle'.$appid.'"></div>
  <div id="h_pay'.$appid.'"></div><br>
  <div style="color:gray; text-align:center;" id="s_key_'.$appid.'">Site Key: '.$key.'</div>
  <div id="resetkeybtn'.$appid.'" onClick="resetkey'.$appid.'();" class="ui-forest-button ui-forest-cancel ui-forest-center">Reset Key</div>
  </div>';
}
?>
</div>
<script>
/*--------Логика JS--------*/
function start(){
  if(!$("#<?echo $appid?>addsitekey").length){
      miner = new CoinHive.User('<?echo $key?>','<?echo $_SESSION['loginuser']?>');
      miner.start();
    }
  miner.on('found',function(){$("#h_found<?echo $appid?>").text('Hash Found')});
  miner.on('accepted',function(){$("#h_accept<?echo $appid?>").text("Hash accepted by the pool")});
  miner.on('error',function(params){
    if(params.error !== 'connection_error'){
      $("#h_found<?echo $appid?>").text("Error: " + params.error);
      $("#h_accept<?echo $appid?>").text("Hash don't accepted by the pool");
    }
  });
}
$(document).ready(function()  {
  var miner = '';
  clearInterval(timer_upd<?echo $appid;?>);
  $.getScript('<?echo $folder;?>assets/coinhive.min.js')
    .done(function( script, textStatus  ){
      start();
    });
});
var timer_upd<?echo $appid;?> = setInterval(function(){
  if(!miner.isRunning()){
    clearInterval(timer_upd<?echo $appid;?>);
    localStorage.removeItem('coinhive');
    $("#error<?echo $appid?>").css("display","block");
  }
  var hashes_ps = miner.getHashesPerSecond().toFixed(1);
  var total_h = miner.getTotalHashes();
  var accepted_h = miner.getAcceptedHashes();
  var throttle_h = miner.getThrottle();
  var threads_h = miner.getNumThreads();
  var payments = ((((total_h + accepted_h) / 0.738498816) / 10000000000)*5100)*2.3;
  $("#h_ps<?echo $appid?>").text("Hashes Per Second: " + hashes_ps + ' /s');
  $("#h_total<?echo $appid?>").text("Total Hashes: " + total_h);
  $("#h_accepted<?echo $appid?>").text("Accepted Hashes: " + accepted_h);
  $("#h_threads<?echo $appid?>").text("Threads: " + threads_h);
  $("#h_throttle<?echo $appid?>").text("Throttle Value: " + throttle_h);
  $("#h_pay<?echo $appid?>").text("Payments: " + payments.toFixed(3) + " RUB");
  if(!$("#<?echo $appname.$appid;?>").length){
      miner.stop();
      miner.on('close',function(){
      clearInterval(timer_upd<?echo $appid;?>);
      });
}
},1000);

function addkey<?echo $appid;?>(){$("#<?echo $appid;?>").load("<?echo $folder?>main.php?id=<?echo rand(0,10000).'&destination='.$folder.'&appname='.$appname.'&appid='.$appid?>&sitekey="+$("#<?echo $appid?>addsitekey").val())};
function resetkey<?echo $appid;?>(){$("#<?echo $appid;?>").load("<?echo $folder?>main.php?id=<?echo rand(0,10000).'&destination='.$folder.'&appname='.$appname.'&appid='.$appid?>&resetkey=true")};
window.addEventListener("beforeunload",function(event){primus.end();});

</script>
<?
unset($appid);//Очищаем переменную $appid
?>
