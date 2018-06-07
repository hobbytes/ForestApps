<?
/* Task Manager */

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$Folder = $_GET['destination'];

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';


/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'Task Manager';
$AppContainer->SecondNameInfo = 'Диспетчер задач';
$AppContainer->VersionInfo = '1.0.1';
$AppContainer->AuthorInfo = 'Forest Media';

/* Container Info */
$AppContainer->appName = $AppName;
$AppContainer->appID = $AppID;
$AppContainer->height = '300px';
$AppContainer->width = '600px';
$AppContainer->customStyle = 'padding-top:0px;';
$AppContainer->StartContainer();

/*--------get lang--------*/
$language  = parse_ini_file('app.lang');

/*--------Логика--------*/
?>
<style>
.tm_box{
  width: 86%;
  padding: 10px;
  border: 1px solid #d4d4d4;
}
.tm-box-left{
  text-align: left;
  table-layout:fixed;
  color: #4654bb;
  cursor: pointer;
}
.tm-box-close{
  float: right;
  color: #69130c;
  background: #e43232;
  padding: 0px 5px 1px 6px;
  margin: 0px 5px;
  border-radius: 5px;
  border-radius: 1px solid #790f0f;
  font-weight: 900;
  font-size: 13px;
}
</style>
<div>
  <table border='1' cellpadding="7" style="border-collapse: collapse; border:1px solid #d4d4d4; width:100%; text-align: center;">
    <tbody id="process_manager<?echo $AppID?>">
      <tr id="process_titles<?echo $AppID?>" style="color:#f2f2f2; background-color:#3a3a3a;">
        <td><?echo $language[$_SESSION['locale'].'_name_title']?></td>
        <td><?echo $language['id_title']?></td>
        <td><?echo $language[$_SESSION['locale'].'_applength']?></td>
        <td><?echo $language[$_SESSION['locale'].'_loc_title']?></td>
      </tr>
      <tbody id="taskcontainer<?echo $AppID?>">
      </div>
    </tbody>
  </table>
	<?
	$AppContainer->EndContainer();
	?>
<script>
clearInterval(timer<?echo $AppID;?>);
var temp_id = 0;
var new_id  = 0;
var new_name = '';
var new_loc = '';
var color<?echo $AppID?> = '#f5f5f5';
$(".process-container").each(function(index, element){
  let p_id = $(element).attr("id");
  let p_name = $("#drag"+p_id + "> .process-title").text();
  let p_loc = $(element).attr("location");
	let _applength = $("#app" + p_id).attr("applength-" + p_id);
	let p_applength = (_applength / 1024).toPrecision(3) + " KB";
  $("#taskcontainer<?echo $AppID?>").append('<tr style="background:'+color<?echo $AppID?>+';" t_id="'+p_id+'" t_applength="'+_applength+'" id="task'+p_id+'"><td>'+p_name+'</td><td>'+p_id+'</td><td>'+p_applength+'</td><td class="tm-box-left""><span onClick="open_folder('+p_id+')">'+p_loc+'</span><div class="tm-box-close ui-forest-blink" onClick="task_close('+p_id+'); checkwindows();">x</div></td></tr>');
  temp_id = p_id;
});

function task_check<?echo $AppID;?>(){
  $(".process-container").each(function(index, element){
    new_id = $(element).attr("id");
    new_name = $("#drag"+new_id + "> .process-title").text();
    new_loc = $(element).attr("location");
		_applength = $("#app" + new_id).attr("applength-" + new_id);
		new_applength = (_applength / 1024).toPrecision(3) + " KB";
  });
  if(new_id > temp_id){
    temp_id = new_id;
		if(color<?echo $AppID?> == '#f5f5f5'){
			color<?echo $AppID?> = '#e0e0e0';
		}else{
			color<?echo $AppID?> = '#f5f5f5';
		}
    $("#taskcontainer<?echo $AppID?>").append('<tr style="background:'+color<?echo $AppID?>+'; t_id="'+new_id+'" t_applength="'+_applength+'" id="task'+new_id+'"><td>'+new_name+'</td><td>'+new_id+'</td><td>'+new_applength+'</td><td class="tm-box-left"><span onClick="open_folder('+new_id+')">'+new_loc+'</span><div class="tm-box-close ui-forest-blink" onClick="task_close('+new_id+'); checkwindows();">x</div></td></tr>');
  }
  $("#taskcontainer<?echo $AppID?> > tr").each(function(index, element){
    var get_id = $(element).attr("t_id");
    if(!$("#process" + get_id).length){
      $("#task"+get_id).remove();
    }

  });
}

function task_close(id){
  $("#process"+id).remove();
  $("#task"+id).remove();
}

function open_folder(id){
var folder = $("#task"+id+' > .tm-box-left').text();
folder = folder.replace('/main.phpx','');
folder = '<?echo $_SERVER['DOCUMENT_ROOT']?>/'+folder+'';
makeprocess('<?echo $_SERVER['DOCUMENT_ROOT'].'/system/apps/Explorer/main.php'?>',folder,'dir','<?echo $language[$_SESSION['locale'].'_explorer_title']?>');
}

var timer<?echo $AppID;?> = setInterval(function(){
  if($("#<?echo $AppName.$AppID;?>").length){
    task_check<?echo $AppID;?>();
}else{
  clearInterval(timer<?echo $AppID;?>);
}
},500);
</script>
<?
unset($AppID);
?>
