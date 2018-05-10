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

<div id="<?echo $appname.$appid?>" style="background-color:#e9eef1; height:550px; width:800px; border-radius:0px 0px 5px 5px; overflow:auto;">

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
	<input id="url-<?echo $appid?>" class="requset-input" style="width:92%;" type="text" value="" placeholder="URL">
	<label for="reqtype-<?echo $appid?>">Type: </label>
	<select id="reqtype-<?echo $appid?>">
		<option value="GET">GET</option>
		<option value="POST">POST</option>
	</select>
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

<div class="requset-container">
<div style="text-align:left; font-size:24px; font-weight:900; color:#5896f5;">
	Response
</div>
<pre id="response-container<?echo $appid?>" style="border:2px solid #607d8b; min-height:200px; white-space:pre-wrap; text-align:left; padding:10px; color:#2a2f31;" contenteditable="true"></pre>
</div>
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
	var returnValue;
	var keyCounter = 0;
	var requsetType = $("#reqtype-<?echo $appid?>").val();
	var keyvalue<?echo $appid?> = {};
	url = $("#url-<?echo $appid?>").val();

  $('.rq').each(function(){
		keyCounter++;
		var key = $(this).val();
		value = ''+$('.requset-input[k="value-'+keyCounter+'"]').val()+'';
		if(key && value){
			keyvalue<?echo $appid?>[''+key+''] = value;
		}
		delete value;
		delete key;
  });
	if(keyvalue<?echo $appid?> && url){
		$.ajax({
			url : ''+url+'',
			method : ''+requsetType+'',
			contentType : "application/json; charset=utf-8",
			/*headers : {
				'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8',
				'X-Requested-With' : 'XMLHttpRequest'
			},*/
			data : keyvalue<?echo $appid?>,
			dataType : 'JSONP',
			async : false,
			success : function(result){
				$("#response-container<?echo $appid?>").html(JSON.stringify(result, undefined, 2));
			},
			error : function(httpReq, status, exception){
				$("#response-container<?echo $appid?>").html(status + " " + exception + "<br>"+ JSON.stringify(httpReq, undefined, 2));
			}
		});
	}
}

UpdateWindow("<?echo $appid?>","<?echo $appname?>");
</script>
