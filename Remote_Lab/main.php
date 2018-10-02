<?
/* Remote Lab */

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$Folder = $_GET['destination'];

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'Remote Lab';
$AppContainer->SecondNameInfo = 'Remote Lab';
$AppContainer->VersionInfo = '1.0.2';
$AppContainer->AuthorInfo = 'Forest Media';

/* Container Info */
$AppContainer->appName = $AppName;
$AppContainer->appID = $AppID;
$AppContainer->LibraryArray = array('filesystem', 'bd');
$AppContainer->height = '50vh';
$AppContainer->width = 'auto';
$AppContainer->customStyle = 'padding-top: 0px;';
$AppContainer->StartContainer();

$fileaction = new fileaction;
$bd = new readbd;
$bd->readglobal2("password","forestusers","login",$_SESSION['superuser']);


/* get localization file */
$localization = parse_ini_file('assets/Lang/'.$_SESSION['locale'].'.lang');

$key = $getdata;//get key

/*--------App Logic--------*/
$dir = $_SERVER['DOCUMENT_ROOT'].'/system/users/'.$_SESSION['loginuser'].'/documents/Remote_Lab/';
$unitFolder = $dir.'/Units';//	units folder
if(!is_dir($dir)){ // check folder
	mkdir($dir);
}

?>

<link rel="stylesheet" href="<?echo $Folder.$fileaction->filehash('assets/style.css','false')?>">
<div style="min-width:600px; width:auto; padding:10px; font-size:20px; font-variant-caps:all-small-caps; background:#fff; border-bottom:1px solid #d9e2e7; color:#447ab7; user-select:none;">
	<div id="applabel<?echo $AppID?>" onclick="back<?echo $AppID?>()" style="width:fit-content; color:#026158; padding:10px; border: 2px solid; border-radius:5px; font-weight:600;">
		Remote Lab
	</div>
</div>
<?
$selectUnit = $_GET['selectunit'];

/* Erase Data */
if(isset($_GET['eraseunit'])){
	unlink($unitFolder.'/'.$_GET['eraseunit'].'/hub.foc');
	$selectUnit = $_GET['eraseunit'];
}

if(!empty($_GET['addunit'])){ //	check new unit
	$unitName = $_GET['addunit'];// Unit Name
	if(!is_dir($unitFolder)){ // check folder
		mkdir($unitFolder);
	}

	$vk_social = NULL;

	if(isset($_POST['customname'])){
		$_token = $_POST['customname'];
		if(preg_match('%vksocial%', $_token)){
			$vk_id = str_replace('vksocial-', '', stristr($_token, 'vksocial-'));
			$vk_social = "vksocial='$vk_id'\n";
		}
	}else{
		$_token = md5($unitName.date('dmyhis'));// generate token
	}

	$token = $security->__encode($_token, $key);
	$TwinUnitError = '';// error flag for new unit block
	$NewUnitFolder = $unitFolder.'/'.$_token;
	if(!is_dir($NewUnitFolder)){
		mkdir($NewUnitFolder);// make folder
		$nameUnit = $_GET['addunit'];
		$FileContent = "[main]\nname='$nameUnit'\ntoken='$token'\ntype='line'\nstep='0'\ncstep='0'\nlabels=''\n$vk_social";// config.foc content
		file_put_contents($NewUnitFolder.'/'.'config.foc',$FileContent);// make config.foc
		$selectUnit = $_token;
	}else{
		$TwinUnitError = 'true';
	}
}

/* Save Unit */
if(isset($_GET['name']) && isset($_GET['step']) && isset($_GET['cstep']) && isset($_GET['chartType'])){

	/* prepare data */
	$_name = $_GET['name'];
	$_step = intval($_GET['step']);
	$_cstep = intval($_GET['cstep']);
	$_type = $_GET['chartType'];

	if($_cstep > $_step){
		$_step = '0';
		$_cstep = '0';
	}

	$newData = array("name='$_name'", "step='$_step'", "cstep='$_cstep'", "type='$_type'");

	/* get temp data */
	$configFile = $unitFolder.'/'.$selectUnit.'/config.foc';
	$configTemp = parse_ini_file($configFile);
	$TepmName = $configTemp['name'];
	$TepmStep = $configTemp['step'];
	$TepmCStep = $configTemp['cstep'];
	$TepmCStep = $configTemp['type'];
	$oldData = array("name='$TepmName'", "step='$TepmStep'", "cstep='$TepmCStep'", "type='$TepmCStep'");

	/* get default config file for replacement */
	$ReplaceConfig = file_get_contents($configFile);
	$SaveConfigData = str_replace($oldData, $newData, $ReplaceConfig);
	file_put_contents($configFile, $SaveConfigData);
}

/* Delete Unit */
if(isset($_GET['deleteunit'])){

	$configFile = parse_ini_file($unitFolder.'/'.$_GET['deleteunit'].'/config.foc');
	if(!empty($configFile['vksocial'])){
		$fuid = $bd->readglobal2("fuid","forestusers","login",$_SESSION["loginuser"], true);
		$password = $bd->readglobal2("password","forestusers","login",$_SESSION["loginuser"], true);
		$d_root = $_SERVER['DOCUMENT_ROOT'];
		$token = md5($fuid.$d_root.$password);
		$data = http_build_query(array('token' => $token, 'id' => $configFile['vksocial']));
		$check = file_get_contents('http://forest.hobbytes.com/media/os/modules/vk/DeleteUser.php?'.$data);
	}
	$fileaction->deleteDir($unitFolder.'/'.$_GET['deleteunit']);
}


/* Load Unit */
if(isset($selectUnit)){ //	check new unit

	$unitName = $selectUnit;// Unit Name
	$hubFile = $unitFolder.'/'.$unitName.'/hub.foc';
	$configFile = $unitFolder.'/'.$unitName.'/config.foc';
	$conditionFile = $unitFolder.'/'.$unitName.'/conditions.foc';

	if(!is_file($conditionFile)){
		file_put_contents($conditionFile, '');
	}else{
		$conditions = file_get_contents($conditionFile);
	}

	$hub = parse_ini_file($hubFile, true);
	$config = parse_ini_file($configFile);
	$input_array = explode(',', $config['labels']);
	$labels = '';

	$count = 0;
	$a = array();
	foreach ($hub as $value => $key){
		array_push($a,$key);
		$count++;
		$ts = date("d.m.y, H:i",$key['timestamp']);//convert unix to date
		$labels = $labels.','."'$ts'";//get labels
	}

	//save condition
	if(isset($_GET['operand1'])){
		$condname = md5($_GET['operand1'].$_GET['operand2'].$_GET['condition'].$_GET['email'].date('dmyhis'));
		$conditions = $conditions."\n\n"."[$condname]\noperand1='".$_GET['operand1']."'\ncondition='".$_GET['condition']."'\noperand2='".$_GET['operand2']."'\nemail='".$_GET['email']."'\nselfd='".$_GET['selfd']."'";
		file_put_contents($conditionFile, $conditions);
	}

	//delete condition
	if(isset($_GET['deletecondition'])){
		$delete_condtition = $_GET['deletecondition'];
			$file = file_get_contents($conditionFile);
			$update_condition = preg_replace("%(?ms)^\[$delete_condtition](?:(?!^\[[^]\r\n]+]).)*%", '', $file);
			file_put_contents($conditionFile, $update_condition);
		}

	$get_conditions = parse_ini_file($conditionFile, true);

	$lastConnection = $value;//last data receive

	function newColor($id){ //get color for chart
		$backgroundColor = array("#ff6384","#36a2eb","#4caf50","#c45850","#4bc0c0","#3e95cd","#ff9800");
		if(!empty($backgroundColor[$id])){
			return	$backgroundColor[$id];
		}else{
			return sprintf('#%06X',mt_rand('0','0xFFFFFF'));
		}
	}

	/*split arrays*/
	$b = array();
	$c = array();
	$minValues = array();
	$maxValues = array();
	$avgValues = array();
	$count_ = 0;
	foreach ($input_array as $keys) {
	$c = array($keys => array_column($a,"$keys"));
	array_push($b,$c);
	array_push($avgValues,array($keys => round(array_sum($b[$count_][$keys])/$count)));
	array_push($minValues,array($keys => min($b[$count_][$keys])));
	array_push($maxValues,array($keys => max($b[$count_][$keys])));
	$count_++;
}


/*get series*/
$Count = 0; //set zero for counter
$series = '';
foreach($b as $test){
		foreach ($test as $key => $value) {
			$color = newColor($Count);// get color
			$Count++;
			$series.="{
				label: '$key',
				backgroundColor: '$color',
				borderColor: '$color',
				data: [";
					foreach ($value as $keys) {
						$series.=$keys.',';
					}
					$series.='],
					fill: false
				},';
		}
}

	$series = str_replace(',]',']',$series);

	$_date = new DateTime();//prerpare for Request URL
	$timestamp = $_date->getTimestamp();
	$RequestURL = 'http://'.$_SERVER['HTTP_HOST'].'/'.$Folder.'hub.php?token='.$unitName.'&user='.$_SESSION['loginuser'].'&#38timestamp='.$timestamp.'&{key1=value1&keyN=valueN}';

	echo '
	<div>
	<div class="lab-unit resizeunit" id="info-unit'.$AppID.'" style="width:auto;">
		<div>
			<div class="unit-fullsize-button" onClick="changesize'.$AppID.'(\'info-unit'.$AppID.'\')">
			</div>
		</div>
		<div class="lab-unit-label">
			'.$localization['info_label'].'
		</div>
		<div style="background:#f9f9f9; padding:10px;">
			<div style="text-align:left;">'.$localization['name_label'].': <span id="name'.$AppID.'" contenteditable="true" class="lab-unit-tag lab-unit-edit">'.$config['name'].'</span></div><br>
			<div style="text-align:left;">'.$localization['input_data_label'].':<div style="display:inline-table; margin-bottom:10px;">
			';
			foreach ($input_array as $data){
				echo '<div class="lab-unit-tag">'.$data.'</div>';
			}
			echo '
			</div>
			</div>
				<div style="text-align:left;">'.$localization['chart_type_label'].':
					<select id="chartType'.$AppID.'" class="lab-unit-tag lab-unit-edit" style="cursor:default;">
						<option value="line">Line</option>
						<option value="bar">Bar</option>
						<option value="radar">Radar</option>
					</select>
				</div><br>
				<div style="text-align:left;">'.$localization['step_label'].': <span id="step'.$AppID.'" contenteditable="true" class="lab-unit-tag lab-unit-edit">'.$config['step'].'</span></div><br>
				<div style="text-align:left;">'.$localization['current_step_label'].': <span id="cstep'.$AppID.'" contenteditable="true" class="lab-unit-tag lab-unit-edit">'.$config['cstep'].'</span></div><br>
				<div style="text-align:left;">'.$localization['last_connection'].': <span class="lab-unit-tag">'.$lastConnection.'</span></div>
		</div><br>
		<div style="text-align:left;">'.$localization['request_label'].': <div class="lab-unit-tag" style="width:270px; font-size:10px; cursor:text;" contenteditable="true">'.$RequestURL.'</div></div>
		<div class="lab-unit-button mode-blue" onclick="saveunit'.$AppID.'()">
			'.$localization['save_button'].'
		</div>
		<div id="eraseButton'.$AppID.'" class="lab-unit-button mode-red" style="background:#808080" messageTitle="'.$localization['erase_mt'].'" messageBody="'.$localization['erase_mb'].'" okButton="'.$localization['erase_ok_btn'].'" cancelButton="'.$localization['cancel_btn'].'" onclick="ExecuteFunctionRequest'.$AppID.'(this, \'EraseData'.$AppID.'\')">
			'.$localization['erase_button'].'
		</div>
		<div id="deleteButton'.$AppID.'"  class="lab-unit-button mode-red" messageTitle="'.$localization['delete_mt'].'" messageBody="'.$localization['delete_mb'].'" okButton="'.$localization['delete_ok_btn'].'" cancelButton="'.$localization['cancel_btn'].'" onclick="ExecuteFunctionRequest'.$AppID.'(this, \'deleteunit'.$AppID.'\')">
			'.$localization['delete_unit'].'
		</div>
	</div>

	<div class="lab-unit resizeunit" style="width:auto;" id="chart-unit'.$AppID.'">
		<div>
			<div class="unit-fullsize-button" onClick="changesize'.$AppID.'(\'chart-unit'.$AppID.'\')">
			</div>
		</div>
		<div class="lab-unit-label">
			'.$localization['charts_label'].'
		</div>
		<div style="width:90%; max-width:100%; min-width:500px;">
			<canvas id="chart'.$AppID.'">
			</canvas>
		</div>
	</div>

	<div class="lab-unit resizeunit" id="stat-unit'.$AppID.'" style="width:auto;">
		<div>
			<div class="unit-fullsize-button" onClick="changesize'.$AppID.'(\'stat-unit'.$AppID.'\')">
			</div>
		</div>
		<div class="lab-unit-label">
			'.$localization['stat_label'].'
		</div>
		<div style="text-align:left;">'.$localization['stat_datar_label'].': <span class="lab-unit-tag">'.$count.'</span></div><br>
		<div class="lab-stat-block"><div class="lab-stat-block-label">'.$localization['stat_max'].'</div>';

		foreach ($maxValues as $value){
			foreach($value as $key => $value_){
				echo '<div style="text-align:left;">'.$key.': <span class="lab-unit-tag">'.$value_.'</span></div><br>';
			}
		}

		echo '</div><div class="lab-stat-block"><div class="lab-stat-block-label">'.$localization['stat_min'].'</div>';

		foreach ($minValues as $value){
			foreach($value as $key => $value_){
				echo '<div style="text-align:left;">'.$key.': <span class="lab-unit-tag">'.$value_.'</span></div><br>';
			}
		}

		echo '</div><div class="lab-stat-block"><div class="lab-stat-block-label">'.$localization['stat_avg'].'</div>';

		foreach ($avgValues as $value){
			foreach($value as $key => $value_){
				echo '<div style="text-align:left;">'.$key.': <span class="lab-unit-tag">'.$value_.'</span></div><br>';
			}
		}

	echo '</div>
	</div>

	<div class="lab-unit resizeunit" style="width:auto;" id="notification'.$AppID.'">
		<div>
			<div class="unit-fullsize-button" onClick="changesize'.$AppID.'(\'notification'.$AppID.'\')">
			</div>
		</div>
		<div class="lab-unit-label">
			'.$localization['not_label'].'
		</div>
		<div style="width:90%; max-width:100%; min-width:300px;">
		'.$localization['not_new_label'].'
			<div style="padding:10px; border-bottom: 1px solid #009688;">
			<div style="text-align:center;">'.$localization['not_if_label'].':
				<select id="operand1'.$AppID.'" class="lab-unit-tag lab-unit-edit" style="cursor:default;">';
			 foreach ($input_array as $data){
 				echo '<option value="'.$data.'">'.$data.'</option>';
 			}
  echo '</select>
				<select id="condition'.$AppID.'" class="lab-unit-tag lab-unit-edit" style="cursor:default;">
				<option value="&gt;">&gt;</option>
				<option value="&lt;">&lt;</option>
				<option value="==">==</option>
				<option value="&gt;=">&gt;=</option>
				<option value="&lt;=">&lt;=</option>
				<option value="!=">!=</option>
				</select>
				<span id="operand2'.$AppID.'" contenteditable="true" class="lab-unit-tag lab-unit-edit">0</span>
			</div>
				'.$localization['not_email_label'].': <input id="email'.$AppID.'" type="email" placeholder="e-mail">
				<div class="lab-unit-tag lab-unit-edit">
					<input style="width:auto;" type="checkbox" id="selfdestr'.$AppID.'" name="selfdestr'.$AppID.'">
					<label for="selfdestr'.$AppID.'">'.$localization['not_selfd_label'].'</label>
				</div>
				<div class="lab-unit-button" onclick="addnotification'.$AppID.'()">
					'.$localization['new_unit_button'].'
				</div></div>
				<div style="padding:10px;">'.$localization['not_allc_label'].'</div>
				<div style="text-align:left; padding:10px;">';
				$i = 0;
				foreach ($get_conditions as $key => $value){
					$selfd = $value['selfd'];
					$i++;
					if($selfd == 'true'){
						$prefix = '('.$localization['not_destr_label'].')';
					}
					echo
				 '<div style="padding:5px; color:#9c27b0;">
						<b>'.$localization['not_cond_label'].'-'.$i.':</b>
						<span class="lab-condition">'.$value['operand1'].' '.$value['condition'].' '.$value['operand2'].'</span>
						<span id="delete'.$i.$AppID.'" messageTitle="'.$localization['cond_mt'].'" messageBody="'.$localization['cond_mb'].'" okButton="'.$localization['cond_ok_btn'].'" cancelButton="'.$localization['cancel_btn'].'" class="lab-delete-condition ui-forest-blink" onClick="ExecuteFunctionRequest'.$AppID.'(this, \'DeleteCondition'.$AppID.'\', \''.$key.'\')">x</span><span style="padding:0px 10px; font-size: 12px; color: #676767;">'.$prefix.'</span>
					</div>';
					unset($prefix, $selfd);
				}
echo	'</div>
		</div>
	</div>

	<div class="lab-unit resizeunit" id="raw-unit'.$AppID.'" style="width:auto;">
		<div>
			<div class="unit-fullsize-button" onClick="changesize'.$AppID.'(\'raw-unit'.$AppID.'\')">
			</div>
		</div>
		<div class="lab-unit-label">
			'.$localization['raw_label'].'
		</div>
		<div style="white-space:pre-wrap; text-align:left; overflow:hidden; overflow-y:auto; padding:10px; border:2px dashed #4dc6fd; cursor:text; background:#f9f9f9; #color:#3a3a3a; height:200px;" contenteditable="true">
		'.file_get_contents($hubFile).'
		</div>
	</div>
	</div>
	';
	?>
	<script>
		var config = {
			type: '<?echo $config['type']?>',
			data: {
				labels: [<?echo ltrim($labels,',')?>],
				datasets: [<?echo $series?>]
			},
			options: {
				responsive: true,
				title: {
					display: true,
					text: '<?echo $config['name']?>'
				},
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: '<?echo $localization['charts_x']?>'
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: '<?echo $localization['charts_y']?>'
						}
					}]
				}
			}
		};


			var ctx = document.getElementById('chart<?echo $AppID?>').getContext('2d');
			window.myLine = new Chart(ctx, config);
			var applabel = $("#applabel<?echo $AppID?>").text();
			$("#applabel<?echo $AppID?>").text("<"+applabel);
			$("#applabel<?echo $AppID?>").addClass('lab-unit-app-button');
			$("#chartType<?echo $AppID?> option[value=<?echo $config['type']?>]").attr('selected', 'selected');
	</script>
	<?
}else{

?>
<div class="lab-unit">
	<div class="lab-unit-label">
		<? echo $localization['new_unit_label'] ?>
	</div>
	<input id="newunit<?echo $AppID?>" type="text" placeholder="<? echo $localization['new_unit_name_label'] ?>">
	<div class="lab-unit-button" onclick="addunit<?echo $AppID?>()">
		<? echo $localization['new_unit_button'] ?>
	</div>
	<?
	if($TwinUnitError == 'true'){
		echo '<div style="margin-top:10px; color:#f44336; font-size:14px;">Unit already exists!</div>';
	}
	?>
</div>
<?
foreach (glob($_SERVER['DOCUMENT_ROOT'].'/system/users/'.$_SESSION['loginuser'].'/documents/Remote_Lab/Units/*/config.foc') as $filenames)
{
	$config = parse_ini_file($filenames); //get config
	$name = $config['name']; //get name
	$token = $security->__decode($config['token'], $key); //get token
	echo '
	<div class="lab-unit" id="'.$token.'" onclick="selectunit'.$AppID.'(this)">
		<div class="lab-unit-label">
			'.$name.'
		</div>
		<div style="font-size:8px;">
			'.$token.'
		</div>
	</div>
	';
}
}

$AppContainer->EndContainer();
?>
<script>
/*--------JS--------*/

$(document).ready(function()  {
	$.getScript("<?echo $Folder.$fileaction->filehash('assets/Chart/Chart.min.js','false')?>");
});


function closeApp<?echo $AppID?>(){
	$('.trash_c').remove();
}

<?

// prepare request
$AppContainer->ExecuteFunctionRequest();

// back button
$AppContainer->Event(
	"back",
	NULL,
	$Folder,
	'main',
	NULL
);

// add unit
$AppContainer->Event(
	"addunit",
	NULL,
	$Folder,
	'main',
	array(
		'addunit' => '"+escape($("#newunit'.$AppID.'").val())+"'
	)
);

// add condition
$AppContainer->Event(
	"addnotification",
	NULL,
	$Folder,
	'main',
	array(
		'operand1' => '"+escape($("#operand1'.$AppID.'").val())+"',
		'condition' => '"+escape($("#condition'.$AppID.'").val())+"',
		'operand2' => '"+escape($("#operand2'.$AppID.'").text())+"',
		'email' => '"+escape($("#email'.$AppID.'").val())+"',
		'selfd' => '"+escape($("#selfdestr'.$AppID.'").is(":checked"))+"',
		'selectunit' => $unitName
	)
);

// delete condition
$AppContainer->Event(
	"DeleteCondition",
	"el",
	$Folder,
	'main',
	array(
		'deletecondition' => '"+el+"',
		'selectunit' => $unitName
	)
);


// save unit
$AppContainer->Event(
	"saveunit",
	NULL,
	$Folder,
	'main',
	array(
		'name' => '"+escape($("#name'.$AppID.'").text())+"',
		'step' => '"+escape($("#step'.$AppID.'").text())+"',
		'cstep' => '"+escape($("#cstep'.$AppID.'").text())+"',
		'chartType' => '"+escape($("#chartType'.$AppID.'").val())+"',
		'selectunit' => $unitName
	)
);

// add unit
$AppContainer->Event(
	"selectunit",
	"el",
	$Folder,
	'main',
	array(
		'selectunit' => '"+el.id+"'
	)
);

// add unit
$AppContainer->Event(
	"deleteunit",
	NULL,
	$Folder,
	'main',
	array(
		'deleteunit' => $unitName
	)
);

// add unit
$AppContainer->Event(
	"EraseData",
	NULL,
	$Folder,
	'main',
	array(
		'eraseunit' => $unitName
	)
);

?>

//changesize function
function changesize<?echo $AppID?>(element){
	element = "#"+element;
	if($(element).hasClass('unit-fullsize')){
		$(element).removeClass('unit-fullsize');
	}else{
		$(element).addClass('unit-fullsize');
	}
}

$(".resizeunit").resizable();
</script>
<?
unset($AppID);
?>
