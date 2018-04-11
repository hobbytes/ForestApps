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

<div style="display:flex; padding:13px; background:#2f2e38; color:#fff; border-bottom: 5px solid #534f61">
	<div style="width:150px; border:3px solid #534f61; margin:4px 10px;" id="scale-display<?echo $appid?>">
		<div id="scale-handle<?echo $appid?>" style="width: auto; min-width:20px; text-align:center; background:#534f61; border:2px solid #706a86; color:#fff;" class="ui-slider-handle"></div>
	</div>
	<div style="width:100%;">
		<div id="colorpalette1_<?echo $appid?>" onclick="setcolor<?echo $appid?>(this)" class="colorpalette ui-forest-blink" style="border-radius:15px; width:20px; height:20px; color:#1d80d0; float:left; background:#fff; border:3px solid; margin:0 5px;">
		</div>
		<div id="colorpalette2_<?echo $appid?>" onclick="setcolor<?echo $appid?>(this)" class="colorpalette ui-forest-blink" style="border-radius:15px; width:20px; height:20px; color:#534f62; float:left; background:#1f1f1f; border:3px solid; margin:0 5px;">
		</div>
		<div id="hidepanel<?echo $appid?>" onclick="HideRightPanel<?echo $appid?>()" style="color:#1d80d0; border:3px solid; width:40px; height:30px; float:right;" class="ui-forest-blink">
			<div style="border-left:3px solid; width:34%; float: right; height:100%;">
			</div>
		</div>
	</div>
</div>

<div style="min-width:700px; min-height:500px; height:91%;">
	<div id="code-pre-container<?echo $appid?>" style="height:100%; min-height:500px; float:right; background-color:#22212b; width:49%; border-left:4px solid #534f61;">
		<textarea id="code-container<?echo $appid?>" style="height:70%; width:100%; min-height:350px; background:#2f2e38; border:3px solid #534f61; color:#fff; padding:10px; border-left:none;  border-top:none;"><?echo $data;?></textarea>
		<div style="text-align:center; padding:10px;">
			<input id="imgname<?echo $appid?>" style="font-size:17px; background:#2f2e38; border:2px solid #534f61; padding:5px; color:#fff;" type="text" value="<?echo $name?>" placeholder="Image Name">
			<select id="selectext<?echo $appid?>" style="font-size:17px; padding:4px; background:#2f2e38; border:2px solid #534f61; color:#fff;">
				<option value="png">png</option>
				<option value="jpg">jpg</option>
			  <option value="bmp">bmp</option>
			</select>
		</div>
		<div id="saveimg<?echo $appid?>" class="ui-forest-button ui-forest-accept ui-forest-center" style="width:80%;">Save</div>
    <div id="saveraw<?echo $appid?>" class="ui-forest-button ui-forest-cancel ui-forest-center" style="width:80%;">Save RAW</div>
	</div>
	<div id="image-container<?echo $appid?>" style="width:100%; height:100%; transition:all 0.2s ease;">
	<div id="image-render<?echo $appid?>" style="position:absolute; top:25%; left:25%; transform: translateX(-50%); cursor:default; background-color:transparent; word-break:break-word; overflow:hidden;">
	</div>
</div>
</div>
</div>
<script>
/*--------JS Logic--------*/

/* Set color */
function setcolor<?echo $appid?>(element){
	$('.colorpalette').css('color','534f62');
	$('#'+element.id+'').css('color','1d80d0');
	getColor = $('#'+element.id+'').css('background');
	$('#image-container<?echo $appid?>').css('background',''+getColor+'');
}

/* Hide Right Panel */
var mode = '0';
function HideRightPanel<?echo $appid?>(){
	if (mode == '0'){
		$("#hidepanel<?echo $appid?>").css('color','#7e7890');
		$('#code-pre-container<?echo $appid?>').hide('slide',{direction:"right"},500);
			mode = '1';
	}else{
		$("#hidepanel<?echo $appid?>").css('color','#1d80d0');
		$('#code-pre-container<?echo $appid?>').show('slide',{direction:"right"},500);
		mode = '0';
	}
}

/* Slider */
scaleHandler = $("#scale-handle<?echo $appid?>");
var scaleValue = '1';
$("#scale-display<?echo $appid?>").slider({
	value: scaleValue,
	min: 0.1,
	max: 2,
	step:0.05,
	create: function(){
		scaleHandler.text(scaleValue);
	},
	slide: function(event, ui){
		scaleHandler.text(ui.value);
		scaleValue = ui.value;
		$('#image-render<?echo $appid?>').css('transform','scale('+scaleValue+')');
	}
});

/* Update */
$($('#code-container<?echo $appid?>').val()).prependTo('#image-render<?echo $appid?>');
$('#image-render<?echo $appid?>').draggable({snap:"#image-container<?echo $appid?>"});
$('#code-container<?echo $appid?>').bind('input propertychange', function(){
	$('#image-render<?echo $appid?>').empty();
	$($('#code-container<?echo $appid?>').val()).prependTo('#image-render<?echo $appid?>');
});

/* Save */
$(function() {
    $("#saveimg<?echo $appid?>").click(function() {
			$('#image-render<?echo $appid?>').css('transform','scale(1)');
        html2canvas($("#image-render<?echo $appid?>").children().get(0), {backgroundColor:'rgba(0,0,0,0)', logging:false, async:true, allowTaint:true}).then(function(canvas){
					var url = "<?echo $folder;?>saveImage";
					var name = $("#imgname<?echo $appid?>").val();
					var user = "<?echo $user?>";
					var ext = $("#selectext<?echo $appid?>").val();
					var imgData = canvas.toDataURL('image/'+ext+'');
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

					$('#image-render<?echo $appid?>').css('transform','scale('+scaleValue+')');
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
