<?
/*--------Get App Name and App ID--------*/
if($_GET['getinfo'] == 'true'){
	include '../../core/library/etc/appinfo.php';
	$appinfo = new AppInfo;
	$appinfo->setInfo('Web Picture', '1.0', 'Forest Media', 'Web Picture');
}
$appname=$_GET['appname'];
$appid=$_GET['appid'];
?>
<div id="<?echo $appname.$appid;?>" style="background-color:#f2f2f2; height:100%; width:100%; border-radius:0px 0px 5px 5px; overflow:auto;">
<?php
/*--------Include Libraries--------*/
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/etc/security.php';
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/filesystem.php';
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/permissions.php';
/*--------Run Session--------*/
session_start();
/*--------Check Security--------*/
$security	=	new security;
$security->appprepare();
$fileaction = new fileaction;
$newpermission = new PermissionRequest;
/*
$click - click or touch action
$folder - folder var
*/
$click=$_GET['mobile'];
$folder=$_GET['destination'];
/*--------PHP Logic--------*/
$user = $_SESSION['loginuser'];
$downloadDir = $_SERVER['DOCUMENT_ROOT'].'/system/users/'.$user.'/documents/images';

$newpermission->fileassociate(array('w2p'), $folder.'main.php', 'webpicloader', $appname);
if(isset($_GET['webpicloader'])){
	$data = file_get_contents($_SERVER['DOCUMENT_ROOT'].$_GET['webpicloader']);
	$name = pathinfo($_SERVER['DOCUMENT_ROOT'].$_GET['webpicloader'])['filename'];
}else{
	$data = '<div style="width:200px; height:200px;">&#13;&#10;</div>';
}
?>
<script src="<?echo $folder.$fileaction->filehash('assets/html2canvas/html2canvas.min.js','false')?>"></script>
<div style="min-width:700px; min-height:500px; height:100%;">
	<div id="code-pre-container<?echo $appid?>" style="height:100%; min-height:500px; float:right; background-color:#22212b; width:49%; border-left:4px solid #534f61;">
		<textarea id="code-container<?echo $appid?>" style="height:70%; width:100%; min-height:350px; background:#2f2e38; border:3px solid #534f61; color:#fff; padding:10px; border-left:none;"><?echo $data;?></textarea>
		<div style="text-align:center; padding:10px;">
			<input id="imgname<?echo $appid?>" style="font-size:17px; background:#2f2e38; border:2px solid #534f61; padding:5px; color:#fff;" type="text" value="<?echo $name?>" placeholder="Image Name">
			<select id="selectext<?echo $appid?>" style="font-size:17px; padding:4px; background:#2f2e38; border:2px solid #534f61; color:#fff;">
			  <option value="jpg">jpg</option>
			  <option value="png">png</option>
			  <option value="bmp">bmp</option>
			</select>
		</div>
		<div id="saveimg<?echo $appid?>" class="ui-forest-button ui-forest-accept ui-forest-center" style="width:80%;">Save</div>
    <div id="saveraw<?echo $appid?>" class="ui-forest-button ui-forest-cancel ui-forest-center" style="width:80%;">Save RAW</div>
	</div>
	<div id="image-container<?echo $appid?>" style="position:absolute; top:25%; left:25%; transform: translateX(-50%); background-color:transparent; word-break:break-word; overflow:hidden;">
	</div>
</div>
</div>
<script>
/*--------JS Logic--------*/

$($('#code-container<?echo $appid?>').val()).prependTo('#image-container<?echo $appid?>');
$('#image-container<?echo $appid?>').draggable();
$('#code-container<?echo $appid?>').bind('input propertychange', function(){
	$('#image-container<?echo $appid?>').empty();
	$($('#code-container<?echo $appid?>').val()).prependTo('#image-container<?echo $appid?>');
});
$(function() {
    $("#saveimg<?echo $appid?>").click(function() {
        html2canvas($("#image-container<?echo $appid?>").children().get(0)).then(function(canvas){
					var imgData = canvas.toDataURL('image/jpeg');
					var url = "<?echo $folder;?>saveImage";
					var name = $("#imgname<?echo $appid?>").val();
					var user = "<?echo $user?>";
					var ext = $("#selectext<?echo $appid?>").val();
					$.ajax({
						type: "POST",
						url: url,
						datatype: 'text',
						data: {
							base64data : imgData,
							name : name,
							ext : ext,
							user : user
						}
					});
					makeprocess("system/apps/Explorer/main.php" , "<?echo $downloadDir?>", "dir", "Explorer");
				})
    });

		$("#saveraw<?echo $appid?>").click(function() {
					var imgData = $('#code-container<?echo $appid?>').val();
					var url = "<?echo $folder;?>saveImage";
					var name = $("#imgname<?echo $appid?>").val();
					var user = "<?echo $user?>";
					$.ajax({
						type: "POST",
						url: url,
						datatype: 'text',
						data: {
							base64data : imgData,
							name : name,
							ext : "w2p",
							user : user
						}
					});
					makeprocess("system/apps/Explorer/main.php" , "<?echo $downloadDir?>", "dir", "Explorer");
    });
});
</script>
