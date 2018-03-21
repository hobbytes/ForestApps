<?
if($_GET['getinfo'] == 'true'){
	include '../../core/library/etc/appinfo.php';
	$appinfo = new AppInfo;
	$appinfo->setInfo('Matchematical Curves', '1.0', 'codepen.io', 'Matchematical Curves');
}
$appname=$_GET['appname'];
$appid=$_GET['appid'];
?>
<div id="<?echo $appname.$appid;?>" style="background-color:#2f2f2f; height:100%; width:100%; padding-top:10px; border-radius:0px 0px 5px 5px; overflow:auto;">
<?
/*--------Подключаем библиотеки--------*/
include '../../core/library/etc/security.php';
$security	=	new security;
$security->appprepare();
/*
Инициализируем переменные
$click - переменная используется для определения действия (клик или прикосновение)
$folder - переменная хранит место запуска программы
*/
$click=$_GET['mobile'];
$folder=$_GET['destination'];
/*--------Запускаем сессию--------*/
session_start();
/*--------Логика--------*/
?>
<script>
$(function (){
$.getScript('<?echo $folder;?>math.js')
  .done(function( script, textStatus  ){
    //console.log('highlight.js is load');
    run<?echo $appid;?>();
  });

  $.getScript('<?echo $folder;?>grapher.js')
    .done(function( script, textStatus  ){
      //console.log('php_file_tree.js is load');
    });
  });
</script>
<div class="container">

<!-- The math expression will be plotted on this Canvas. -->
<div style="text-align:center; margin: 30px;">
      <canvas id="myCanvas<?echo $appid;?>" style="width: 450px;  height: 450px; background: #2f2f2f; border:4px #cacaca solid;"></canvas>
      <div id="statfunc<?echo $appid;?>" style="color:#9cffff; padding:5px; width:100%; text-align:left;">---</div>
</div>

<!-- This text field will contain the math expression to plot. -->

<div style="text-align:center; padding-bottom:20px;">
  <div style="margin:10px auto;">
  <div id="zoomin<?echo $appid;?>" class="ui-button ui-widget ui-corner-all">
    <span class="ui-icon ui-icon-zoomin"></span>
  </div>
  <div id="zoomout<?echo $appid;?>" class="ui-button ui-widget ui-corner-all">
    <span class="ui-icon ui-icon-zoomout"></span>
  </div>

  <div id="speedup<?echo $appid;?>" class="ui-button ui-widget ui-corner-all">
    <span class="ui-icon ui-icon-plus"></span>
  </div>
  <div id="speeddown<?echo $appid;?>" class="ui-button ui-widget ui-corner-all">
    <span class="ui-icon ui-icon-minus"></span>
  </div>
</div>
  <input style="font-size:20px;" id="inputField<?echo $appid;?>" value="sin(x-t)+cos(x-t)">
  <div id="funcrun<?echo $appid;?>" class="ui-button ui-widget ui-corner-all">
    <span class="ui-icon ui-icon-play"></span>Run
  </div>
</div>

  </div>

</div>
<script>
/*--------Логика JS--------*/

function update<?echo $appid;?>(){
  $("#<?echo $appid;?>").load("<?echo $folder;?>main.php?function="+$("#command<?echo $appid;?>").val()+"&id=<?echo rand(0,10000).'&appid='.$appid.'&mobile='.$click.'&appname='.$appname.'&destination='.$folder;?>")
};

function run<?echo $appid;?>(){
  var canvas = $('#myCanvas<?echo $appid;?>')[0],
      c = canvas.getContext('2d'),
      n = 100,
      xMin = -10,
      xMax = 10,
      yMin = -10,
      yMax = 10,
      math = mathjs(),
      expr = 'sin(x-t)+cos(x-t)',
      defaultExpr = 'sin(x-t)+cos(x-t)',
      scope = {
        x: 0,
        t: 0
      },
      tree,
      time = 0,
      timeIncrement = 0.05;

  //setExprFromHash();
  var input = $('#inputField<?echo $appid;?>');
  input.val(expr);
  setExpr(input.val());
  zoomin();
  zoomout();
  speedup();
  speeddown();
  initTextField();
  startAnimation();


  function setExpr(newExpr){
    expr = newExpr;
    tree = math.parse(expr, scope);
  }

  function initTextField(){
    var input = $('#inputField<?echo $appid;?>');
    input.val(expr);
    var funcrun = $('#funcrun<?echo $appid;?>')
    funcrun.click(function (event) {
      setExpr(input.val());
    });
  }

  function zoomin(){
    var funcrun = $('#zoomin<?echo $appid;?>')
    funcrun.click(function (event) {
    c.translate(-150, -75);
    c.scale(2,2);
    });
  }

  function zoomout(){
    var funcrun = $('#zoomout<?echo $appid;?>')
    funcrun.click(function (event) {
      c//.translate(canvas.width/4, 0);
      //c.scale(0.99,0.99);
      c.setTransform(1,0,0,1,0,0);
    });
  }

  function updatestat(Xx, Yy){
    $('#statfunc<?echo $appid;?>').html('speed: ' + Math.floor(timeIncrement*100)/100 + ' | x: ' + Math.floor(Xx*100)/100 + ', y: ' + Math.floor(Yy*100)/100);
  }

  function speedup(){
    var funcrun = $('#speedup<?echo $appid;?>')
    funcrun.click(function (event) {
      timeIncrement = timeIncrement+0.01;
    });
  }

  function speeddown(){
    var funcrun = $('#speeddown<?echo $appid;?>')
    funcrun.click(function (event) {
      timeIncrement = timeIncrement-0.01;
    });
  }


function startAnimation(){
    (function animloop(){
      requestAnimationFrame(animloop);
      render();
    }());
  }

  function render(){

    time += timeIncrement;

    drawCurve();

    c.beginPath();
    c.strokeStyle="#f2f2f2";
    c.lineWidth=1;
    c.moveTo(canvas.width/2,0);
    c.lineTo(canvas.width/2,canvas.height);
    c.moveTo(0,canvas.height/2);
    c.lineTo(canvas.width,canvas.height/2);
    c.stroke();

    //c.font = "5px Arial";
    //c.fillStyle = "red"
    //c.fillText('0', canvas.width/2+5, canvas.height/2-5);
  }

  function drawCurve(){
    var i,
        xPixel, yPixel,
        percentX, percentY,
        mathX, mathY;

    c.clearRect(0, 0, canvas.width, canvas.height);

    c.beginPath();

    for(i = 0; i < n; i++) {

      percentX = i / (n - 1);

      mathX = percentX * (xMax - xMin) + xMin;

      mathY = evaluateMathExpr(mathX);

      percentY = (mathY - yMin) / (yMax - yMin);

      percentY = 1 - percentY;

      xPixel = percentX * canvas.width;
      yPixel = percentY * canvas.height;

      c.lineTo(xPixel, yPixel);

      updatestat(percentX, percentY);

    }


    c.imageSmoothingEnabled = false;
    c.strokeStyle="#21bf27";
    c.lineWidth=0.5;
    c.stroke();
  }

  function evaluateMathExpr(mathX){
    scope.x = mathX;
    scope.t = time;
    if(tree.eval()){
      return tree.eval();
    }
  }
};
</script>
<?
unset($appid);//Очищаем переменную $appid
?>
