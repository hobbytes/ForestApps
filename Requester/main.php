<?

/*--------App Information--------*/

if($_GET['getinfo'] == 'true'){
	require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/etc/appinfo.php';
	$appinfo = new AppInfo;
	$appinfo->setInfo('Requester', '1.0', 'Forest Media', 'Requester');
}

/*--------Get App Name and App ID--------*/

$appname = $_GET['appname'];
$appid = $_GET['appid'];

?>

<div id="<?echo $appname.$appid;?>" style="background-color:#e9eef1; height:100%; width:100%; border-radius:0px 0px 5px 5px; overflow:auto;">

<style>

.requset-container{
	background: #fff;
	color:#8a8a8a;
	box-shadow: 0 1px 2px rgba(0,0,0,0.1);
	border-radius: 2px;
	border-bottom: 3px solid #fff;
	width:90%;
	margin:20px auto;
	padding:10px;
	text-align: center;
}

.requset-input{
	padding: 10px;
	margin: 10px;
	width: 44%;
}

.requset-button{
	padding: 10px;
	margin: 0 auto;
	width: max-content;
	background: #5896f5;
	color:#fff;
	cursor: default;
	user-select: none;
}
.requset-button:hover{
	background: #71a7fb;
}
</style>

<div class="requset-container">
<div style="text-align:left; font-size:24px; font-weight:900; color:#5896f5;">
	URL
</div>
<input class="requset-input" style="width:92%;" type="text" value="" placeholder="URL">
</div>

<div class="requset-container">
<div style="text-align:left; font-size:24px; font-weight:900; color:#f56258;">
	Request Data
</div>
<div id="input-container-<?echo $appid?>">
<input class="requset-input rq" t="key-1" type="text" value="" placeholder="key 1">
<input class="requset-input" k="value-1" type="text" value="" placeholder="value 1">
<input class="requset-input rq" t="key-2" type="text" value="" placeholder="key 2">
<input class="requset-input" k="value-2" type="text" value="" placeholder="value 2">
<input class="requset-input rq" t="key-3" type="text" value="" placeholder="key 3">
<input class="requset-input" k="value-3" type="text" value="" placeholder="value 3">
</div>
<div class="requset-button" onclick="AddField<?echo $appid?>()">Add</div>
</div>


<div class="requset-button" onclick="Request<?echo $appid?>()">Request</div>

<?php
/*--------Include Libraries--------*/

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/etc/security.php';

/*--------Run Session--------*/

session_start();

/*--------Check Security--------*/

$security	=	new security;
$security->appprepare();

/* $click - click or touch action; $folder - application directory */

$click=$_GET['mobile'];
$folder=$_GET['destination'];

/*--------PHP Logic--------*/

?>
</div>

<script>

/*--------JS Logic--------*/

/*---Add new field---*/
var inputCounter = 3;
function AddField<?echo $appid?>(){
	inputCounter++;
	$('#input-container-<?echo $appid?>').append('<input class="requset-input rq" t="key-'+inputCounter+'" type="text" value="" placeholder="key '+inputCounter+'"> <input class="requset-input" k="value-'+inputCounter+'" type="text" value="" placeholder="value '+inputCounter+'">');
}

/*---Make Request---*/
function Request<?echo $appid?>(){
	var keyCounter = 0;
	var keyvalue<?echo $appid;?> = '';
  $('.rq').each(function(){
		keyCounter++;
		if(this.value && $('.requset-input[k="value-'+keyCounter+'"]').val()){
			keyvalue<?echo $appid;?> = keyvalue<?echo $appid;?> + this.value + ':' + $('.requset-input[k="value-'+keyCounter+'"]').val() + ',';
		}
  });
	keyvalue<?echo $appid;?> = keyvalue<?echo $appid;?>.slice(0,-1);
	if(keyvalue<?echo $appid;?>){
		
	}
}

</script>
