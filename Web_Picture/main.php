<?
/* Web Picture */

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$Folder = $_GET['destination'];

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'Web Picture';
$AppContainer->SecondNameInfo = 'Web Picture';
$AppContainer->VersionInfo = '1.0.1';
$AppContainer->AuthorInfo = 'Forest Media';

/* Container Info */
$AppContainer->appName = $AppName;
$AppContainer->appID = $AppID;
$AppContainer->LibraryArray = array('filesystem', 'permissions');
$AppContainer->height = '100%';
$AppContainer->width = '100%';
$AppContainer->customStyle = 'padding-top:0px;';
$AppContainer->StartContainer();

$fileaction = new fileaction;
$newpermission = new PermissionRequest;

$Folder = $_GET['destination'];

$user = $_SESSION['loginuser'];

$downloadDir = $_SERVER['DOCUMENT_ROOT'].'/system/users/'.$user.'/documents/images';

if(!file_exists($downloadDir)){
	mkdir($downloadDir, 0777, true);
}

$newpermission->fileassociate(array('w2p'), $Folder.'main.php', 'webpicloader', $AppName);

if(isset($_GET['webpicloader'])){
	$data = file_get_contents($_SERVER['DOCUMENT_ROOT'].$_GET['webpicloader']);
	$name = pathinfo($_SERVER['DOCUMENT_ROOT'].$_GET['webpicloader'])['filename'];
}else{
	$data = '<div style="width:200px; height:200px; color: #FFF; background: linear-gradient(to top, #67b26f, #4ca2cd); padding: 20px; border-radius: 50px"><div style="font-weight: 600; font-size: 50px; padding-top:90px; text-align: center;">Web Picture</div></div>';
}

?>

<div style="display:flex; padding:13px; background:#2f2e38; color:#fff; border-bottom: 5px solid #534f61">
	<div style="width:150px; border:3px solid #534f61; margin:4px 10px;" id="scale-display<?echo $AppID?>">
		<div id="scale-handle<?echo $AppID?>" style="width: auto; min-width:20px; text-align:center; background:#534f61; border:2px solid #706a86; color:#fff;" class="ui-slider-handle"></div>
	</div>
	<div style="width:100%;">
		<div id="colorpalette1_<?echo $AppID?>" onclick="setcolor<?echo $AppID?>(this)" class="colorpalette ui-forest-blink" style="border-radius:15px; width:20px; height:20px; color:#1d80d0; float:left; background-color:#fff; border:3px solid; margin:0 5px;">
		</div>
		<div id="colorpalette2_<?echo $AppID?>" onclick="setcolor<?echo $AppID?>(this)" class="colorpalette ui-forest-blink" style="border-radius:15px; width:20px; height:20px; color:#534f62; float:left; background-color:#1f1f1f; border:3px solid; margin:0 5px;">
		</div>
		<div id="hidepanel<?echo $AppID?>" onclick="HideRightPanel<?echo $AppID?>()" style="color:#1d80d0; border:3px solid; width:40px; height:30px; float:right;" class="ui-forest-blink">
			<div style="border-left:3px solid; width:34%; float: right; height:100%;">
			</div>
		</div>
	</div>
</div>

<div id="workplace-container<?echo $AppID?>" style="min-width:700px; min-height:500px; height:91%; transition:all 0.2s ease;">
	<div id="code-pre-container<?echo $AppID?>" style="height:100%; min-height:500px; float:right; background-color:#22212b; width:49%; border-left:4px solid #534f61;">
		<textarea id="code-container<?echo $AppID?>" style="height:70%; width:100%; min-height:350px; background:#2f2e38; border:3px solid #534f61; color:#fff; padding:10px; border-left:none;  border-top:none;"><?echo $data;?></textarea>
		<div style="text-align:center; padding:10px;">
			<input id="imgname<?echo $AppID?>" style="font-size:17px; background:#2f2e38; border:2px solid #534f61; padding:5px; color:#fff;" type="text" value="<?echo $name?>" placeholder="Image Name">
			<select id="selectext<?echo $AppID?>" style="font-size:17px; padding:4px; background:#2f2e38; border:2px solid #534f61; color:#fff;">
				<option value="png">png</option>
				<option value="jpg">jpg</option>
			  <option value="bmp">bmp</option>
			</select>
		</div>
		<div id="saveimg<?echo $AppID?>" class="ui-forest-button ui-forest-accept ui-forest-center" style="width:80%;">Save</div>
    <div id="saveraw<?echo $AppID?>" class="ui-forest-button ui-forest-cancel ui-forest-center" style="width:80%;">Save RAW</div>
	</div>
	<div id="image-container<?echo $AppID?>" style="width:100%; height:100%;">
	<div id="image-render<?echo $AppID?>" style="position:absolute; top:25%; left:25%; transform: translateX(-50%); cursor:default; background-color:transparent; word-break:break-word; overflow:hidden;">
	</div>
</div>
</div>
<?
$AppContainer->EndContainer();
?>
<script>
/*--------JS Logic--------*/

$(document).ready(function()  {
	$.getScript("<?echo $Folder.$fileaction->filehash('assets/html2canvas/html2canvas.min.js','false')?>");
});

/* Set color */
function setcolor<?echo $AppID?>(element){
	$('.colorpalette').css('color','534f62');
	$('#'+element.id+'').css('color','1d80d0');
	getColor = $('#'+element.id+'').css('background-color');
	$('#workplace-container<?echo $AppID?>').css('background-color',''+getColor+'');
}

/* Hide Right Panel */
var mode = '0';
function HideRightPanel<?echo $AppID?>(){
	if (mode == '0'){
		$("#hidepanel<?echo $AppID?>").css('color','#7e7890');
		$('#code-pre-container<?echo $AppID?>').hide('slide',{direction:"right"},500);
			mode = '1';
	}else{
		$("#hidepanel<?echo $AppID?>").css('color','#1d80d0');
		$('#code-pre-container<?echo $AppID?>').show('slide',{direction:"right"},500);
		mode = '0';
	}
}

/* Slider */
scaleHandler = $("#scale-handle<?echo $AppID?>");
var scaleValue = '1';
$("#scale-display<?echo $AppID?>").slider({
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
		$('#image-render<?echo $AppID?>').css('transform','scale('+scaleValue+')');
	}
});

/* Update */
$($('#code-container<?echo $AppID?>').val()).prependTo('#image-render<?echo $AppID?>');
$('#image-render<?echo $AppID?>').draggable({snap:"#image-container<?echo $AppID?>"});
$('#code-container<?echo $AppID?>').bind('input propertychange', function(){
	$('#image-render<?echo $AppID?>').empty();
	$($('#code-container<?echo $AppID?>').val()).prependTo('#image-render<?echo $AppID?>');
});

/* Save */
$(function() {
    $("#saveimg<?echo $AppID?>").click(function() {
			$('#image-render<?echo $AppID?>').css('transform','scale(1)');
        html2canvas($("#image-render<?echo $AppID?>").children().get(0), {backgroundColor:'rgba(0,0,0,0)', logging:false, async:true, allowTaint:true}).then(function(canvas){
					var url = "<?echo $Folder;?>saveImage";
					var name = $("#imgname<?echo $AppID?>").val();
					var user = "<?echo $user?>";
					var ext = $("#selectext<?echo $AppID?>").val();
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

					$('#image-render<?echo $AppID?>').css('transform','scale('+scaleValue+')');
					makeprocess("system/apps/Explorer/main.php" , "<?echo $downloadDir?>", "dir", "Explorer");
				})
    });

		$("#saveraw<?echo $AppID?>").click(function() {
					var imgData = $('#code-container<?echo $AppID?>').val();
					var url = "<?echo $Folder;?>saveImage";
					var name = $("#imgname<?echo $AppID?>").val();
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
