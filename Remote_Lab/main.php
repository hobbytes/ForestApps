<?
/*--------Получаем App Name и App ID--------*/
if($_GET['getinfo'] == 'true'){
	include '../../core/library/etc/appinfo.php';
	$appinfo = new AppInfo;
	$appinfo->setInfo('Remote Lab', '1.0', 'Forest Media', 'Remote Lab');
}
$appname=$_GET['appname'];
$appid=$_GET['appid'];
?>
<div id="<?echo $appname.$appid;?>" style="background-color:#e9eef1; height:100%; width:100%; border-radius:0px 0px 5px 5px; overflow:hidden; overflow-y:auto;">
<?php
/*--------Подключаем библиотеки--------*/
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/etc/security.php';
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/bd.php';
require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/filesystem.php';
/*--------Запускаем сессию--------*/
session_start();
/*--------Проверяем безопасность--------*/
$security	=	new security;
$security->appprepare();

$fileaction = new fileaction;
$bd = new readbd;
$bd->readglobal2("password","forestusers","login",$_SESSION['superuser']);

$key = $getdata;//get key

$click	=	$_GET['mobile'];
$folder	=	$_GET['destination'];
/*--------App Logic--------*/
$dir = $_SERVER['DOCUMENT_ROOT'].'/system/users/'.$_SESSION['loginuser'].'/documents/Remote_Lab/';
$unitFolder = $dir.'/Units';//	units folder
if(!is_dir($dir)){ // check folder
	mkdir($dir);
}

?>
<link rel="stylesheet" href="<?echo $folder.$fileaction->filehash('assets/Chart/style.css','false')?>">
<link rel="stylesheet" href="<?echo $folder.$fileaction->filehash('assets/style.css','false')?>">
<script src="<?echo $folder.$fileaction->filehash('assets/Chart/Chart.min.js','false')?>"></script>
<script src="<?echo $folder.$fileaction->filehash('assets/Chart/utils.js','false')?>"></script>
<div style="min-width:600px; width:100%; padding:10px; font-size:37px; font-variant-caps:all-small-caps; background:#fff; border-bottom:1px solid #d9e2e7; color:#447ab7; user-select:none;">
	Remote Lab
</div>
<?
if(!empty($_GET['addunit'])){ //	check new unit
	$unitName = $_GET['addunit'];// Unit Name
	if(!is_dir($unitFolder)){ // check folder
		mkdir($unitFolder);
	}

	$token = md5($unitName.date('dmyhis'));// generate token
	$token = $security->__encode($token, $key);
	$TwinUnitError = '';// error flag for new unit block
	$NewUnitFolder = $unitFolder.'/'.md5($unitName.date('dmyhis'));
	if(!is_dir($NewUnitFolder)){
		mkdir($NewUnitFolder);// make folder
		$FileContent = "[main]\nname=".$_GET['addunit']."\ntoken='$token'\nstep='0'\ncstep='0'\nlabels=''\n";// config.foc content
		file_put_contents($NewUnitFolder.'/'.'config.foc',$FileContent);// make config.foc
	}else{
		$TwinUnitError = 'true';
	}
}
if(!empty($_GET['selectunit'])){ //	check new unit
	$unitName = $_GET['selectunit'];// Unit Name
	$hubFile = $unitFolder.'/'.$unitName.'/hub.foc';
	$configFile = $unitFolder.'/'.$unitName.'/config.foc';
	$hub = parse_ini_file($hubFile,true);
	$config = parse_ini_file($configFile);
	$input_array = explode(',',$config['labels']);
	$labels = '';
	$series = '';
	$count = 0;
	$a = array();
	foreach ($hub as $value => $key){
		$count++;
		$ts = gmdate("d.m.y",$key['timestamp']);
		foreach ($input_array as $keys) {
			array_push($a,$key);
		}
		$labels = $labels.','."'$ts'";
	}

	$series_ = '';
	$b = array();
	$c = array();
	$t='';
	foreach ($input_array as $keys) {
	$c = array($keys => array_column($a,"$keys"));
	array_push($b,$c);
	$t.= "'$keys',";
}

function newColor($id){
	$backgroundColor = array("#ff6384","#36a2eb","#4caf50","#c45850","#4bc0c0","#3e95cd","#ff9800");
	return	$backgroundColor[$id];
}

$colorCount = 0;

foreach($b as $test){
		foreach ($test as $key => $value) {
			$color = newColor($colorCount);
			$colorCount++;
			$series.="{
				label: '$key',
				backgroundColor: '$color',
				borderColor: '$color',
				data: [";
					foreach ($value as $keys) {
						$series.=$keys.',';
					}
					$series.='],
					fill: false,
				},';
		}
}

	echo '
	<div>
	<div class="lab-unit resizeunit" style="width:auto;">
		<div class="lab-unit-label">
			Info
		</div>
		<div style="background:#f9f9f9; padding:10px;">
			<div style="text-align:left;">Name: <span class="lab-unit-tag">'.$config['name'].'</span></div><br>
			<div style="text-align:left;">Data received: <span class="lab-unit-tag">'.$count.'</span></div><br>
			Intput data:<div style="display:inline-table; margin-bottom:10px;">
			';
			foreach ($input_array as $data){
				echo '<div class="lab-unit-tag">'.$data.'</div>';
			}
			echo '
			</div>
				<div style="text-align:left;">Step: <span class="lab-unit-tag">'.$config['step'].'</span></div><br>
				<div style="text-align:left;">Current step: <span class="lab-unit-tag">'.$config['cstep'].'</span></div><br>
		</div>
	</div>

	<div class="lab-unit resizeunit" style="width:auto;">
		<div class="lab-unit-label">
			Charts
		</div>
		<div style="width:90%; max-width:100%; min-width:500px;">
			<canvas id="chart'.$appid.'">
			</canvas>
		</div>
	</div>

	<div class="lab-unit resizeunit" style="width:auto;">
		<div class="lab-unit-label">
			RAW Data
		</div>
		<div style="white-space:pre-wrap; text-align:left; overflow:hidden; overflow-y:auto; padding:10px; border:2px solid #00bcd4; background:#f9f9f9; #color:#3a3a3a; height:200px;" contenteditable="true">
		'.file_get_contents($hubFile).'
		</div>
	</div>
	</div>
	';
	?>
	<?//echo ltrim($labels,',')?>
	<?//echo $series?>
	<?//echo ltrim($t,',')?>
	<script>
		var config = {
			type: 'line',
			data: {
				labels: [<?echo ltrim($labels,',')?>],
				datasets: [<?echo $series?>]
			},
			options: {
				responsive: true,
				title: {
					display: true,
					text: 'Chart.js Line Chart'
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


			var ctx = document.getElementById('chart<?echo $appid?>').getContext('2d');
			window.myLine = new Chart(ctx, config);
	</script>
	<?
}else{

?>
<div class="lab-unit">
	<div class="lab-unit-label">
		New Unit
	</div>
	<input id="newunit<?echo $appid;?>" type="text" placeholder="Unit Name">
	<div class="lab-unit-button" onclick="addunit<?echo $appid;?>()">
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
	<div class="lab-unit" id="'.$token.'" onclick="selectunit'.$appid.'(this)">
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
?>
</div>
<script>
/*--------Логика JS--------*/
function addunit<?echo $appid;?>(){$("#<?echo $appid;?>").load("<?echo $folder;?>/main.php?addunit="+escape($("#newunit<?echo $appid;?>").val())+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&destination='.$folder;?>")};
function selectunit<?echo $appid;?>(el){$("#<?echo $appid;?>").load("<?echo $folder;?>/main.php?selectunit="+el.id+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&destination='.$folder;?>")};
$(".resizeunit").resizable();

</script>
<?
unset($appid);
?>
