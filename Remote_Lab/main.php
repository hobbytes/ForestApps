<?
/* Web Picture */

$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$Folder = $_GET['destination'];

require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';

/* Make new container */
$AppContainer = new AppContainer;

/* App Info */
$AppContainer->AppNameInfo = 'Remote Lab';
$AppContainer->SecondNameInfo = 'Remote Lab';
$AppContainer->VersionInfo = '1.0.1';
$AppContainer->AuthorInfo = 'Forest Media';

/* Container Info */
$AppContainer->appName = $AppName;
$AppContainer->appID = $AppID;
$AppContainer->LibraryArray = array('filesystem', 'bd');
$AppContainer->height = '100%';
$AppContainer->width = '100%';
$AppContainer->customStyle = 'padding-top:0px; overflow-y:auto;';
$AppContainer->StartContainer();

$fileaction = new fileaction;
$bd = new readbd;
$bd->readglobal2("password","forestusers","login",$_SESSION['superuser']);

$key = $getdata;//get key

/*--------App Logic--------*/
$dir = $_SERVER['DOCUMENT_ROOT'].'/system/users/'.$_SESSION['loginuser'].'/documents/Remote_Lab/';
$unitFolder = $dir.'/Units';//	units folder
if(!is_dir($dir)){ // check folder
	mkdir($dir);
}

?>

<link rel="stylesheet" href="<?echo $Folder.$fileaction->filehash('assets/style.css','false')?>">
<div style="min-width:600px; width:100%; padding:10px; font-size:20px; font-variant-caps:all-small-caps; background:#fff; border-bottom:1px solid #d9e2e7; color:#447ab7; user-select:none;">
	<div id="applabel<?echo $AppID?>" onclick="back<?echo $AppID?>()" style="width:fit-content; color:#026158; padding:10px; border: 2px solid; border-radius:5px; font-weight:600;">
		Remote Lab
	</div>
</div>
<?
$selectUnit = $_GET['selectunit'];

if(!empty($_GET['addunit'])){ //	check new unit
	$unitName = $_GET['addunit'];// Unit Name
	if(!is_dir($unitFolder)){ // check folder
		mkdir($unitFolder);
	}

	$_token = md5($unitName.date('dmyhis'));// generate token
	$token = $security->__encode($_token, $key);
	$TwinUnitError = '';// error flag for new unit block
	$NewUnitFolder = $unitFolder.'/'.md5($unitName.date('dmyhis'));
	if(!is_dir($NewUnitFolder)){
		mkdir($NewUnitFolder);// make folder
		$nameUnit = $_GET['addunit'];
		$FileContent = "[main]\nname='$nameUnit'\ntoken='$token'\ntype='line'\nstep='0'\ncstep='0'\nlabels=''\n";// config.foc content
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
	$fileaction->deleteDir($unitFolder.'/'.$_GET['deleteunit']);
}

/* Load Unit */
if(isset($selectUnit)){ //	check new unit

	$unitName = $selectUnit;// Unit Name
	$hubFile = $unitFolder.'/'.$unitName.'/hub.foc';
	$configFile = $unitFolder.'/'.$unitName.'/config.foc';
	$hub = parse_ini_file($hubFile,true);
	$config = parse_ini_file($configFile);
	$input_array = explode(',',$config['labels']);
	$labels = '';
	$count = 0;
	$a = array();
	foreach ($hub as $value => $key){
		array_push($a,$key);
		$count++;
		$ts = gmdate("d.m.y, H:i",$key['timestamp']);//convert unix to date
		$labels = $labels.','."'$ts'";//get labels
	}

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
	$RequestURL = $_SERVER['HTTP_HOST'].'/'.$Folder.'hub.php?token='.$unitName.'&user='.$_SESSION['loginuser'].'&#38timestamp='.$timestamp.'&{data=value}';

	echo '
	<div>
	<div class="lab-unit resizeunit" style="width:auto;">
		<div class="lab-unit-label">
			Info
		</div>
		<div style="background:#f9f9f9; padding:10px;">
			<div style="text-align:left;">Name: <span id="name'.$AppID.'" contenteditable="true" class="lab-unit-tag lab-unit-edit">'.$config['name'].'</span></div><br>
			<div style="text-align:left;">Intput data:<div style="display:inline-table; margin-bottom:10px;">
			';
			foreach ($input_array as $data){
				echo '<div class="lab-unit-tag">'.$data.'</div>';
			}
			echo '
			</div>
			</div>
				<div style="text-align:left;">Chart Type:
					<select id="chartType'.$AppID.'" class="lab-unit-tag lab-unit-edit" style="cursor:default;">
						<option value="line">Line</option>
						<option value="bar">Bar</option>
						<option value="radar">Radar</option>
					</select>
				</div><br>
				<div style="text-align:left;">Step: <span id="step'.$AppID.'" contenteditable="true" class="lab-unit-tag lab-unit-edit">'.$config['step'].'</span></div><br>
				<div style="text-align:left;">Current step: <span id="cstep'.$AppID.'" contenteditable="true" class="lab-unit-tag lab-unit-edit">'.$config['cstep'].'</span></div><br>
				<div style="text-align:left;">Last connection: <span class="lab-unit-tag">'.$lastConnection.'</span></div>
		</div><br>
		<div style="text-align:left;">Request URL: <div class="lab-unit-tag" style="width:270px; font-size:10px; cursor:text;" contenteditable="true">'.$RequestURL.'</div></div>
		<div class="lab-unit-button mode-blue" onclick="saveunit'.$AppID.'()">
			Save
		</div>
		<div class="lab-unit-button mode-red" onclick="deleteunit'.$AppID.'()">
			Delete Unit
		</div>
	</div>

	<div class="lab-unit resizeunit" style="width:auto;">
		<div class="lab-unit-label">
			Charts
		</div>
		<div style="width:90%; max-width:100%; min-width:500px;">
			<canvas id="chart'.$AppID.'">
			</canvas>
		</div>
	</div>

	<div class="lab-unit resizeunit" style="width:auto;">
		<div class="lab-unit-label">
			Statistics
		</div>
		<div style="text-align:left;">Data received: <span class="lab-unit-tag">'.$count.'</span></div><br>
		<div class="lab-stat-block"><div class="lab-stat-block-label">Max values</div>';

		foreach ($maxValues as $value){
			foreach($value as $key => $value_){
				echo '<div style="text-align:left;">'.$key.': <span class="lab-unit-tag">'.$value_.'</span></div><br>';
			}
		}

		echo '</div><div class="lab-stat-block"><div class="lab-stat-block-label">Min values</div>';

		foreach ($minValues as $value){
			foreach($value as $key => $value_){
				echo '<div style="text-align:left;">'.$key.': <span class="lab-unit-tag">'.$value_.'</span></div><br>';
			}
		}

		echo '</div><div class="lab-stat-block"><div class="lab-stat-block-label">Average values</div>';

		foreach ($avgValues as $value){
			foreach($value as $key => $value_){
				echo '<div style="text-align:left;">'.$key.': <span class="lab-unit-tag">'.$value_.'</span></div><br>';
			}
		}

	echo '</div>
	</div>

	<div class="lab-unit resizeunit" style="width:auto;">
		<div class="lab-unit-label">
			RAW Data
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
							labelString: 'Date'
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Value'
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
		New Unit
	</div>
	<input id="newunit<?echo $AppID?>" type="text" placeholder="Unit Name">
	<div class="lab-unit-button" onclick="addunit<?echo $AppID?>()">
		Create
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

<?
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
?>

$(".resizeunit").resizable();
</script>
<?
unset($AppID);
?>
