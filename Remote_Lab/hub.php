<?
if(isset($_GET)){
  $array = $_GET;
  $timestamp = $_GET['timestamp'];
}else{
  $array = $_POST;
  $timestamp = $_POST['timestamp'];
}

if(!empty($array) && !empty($timestamp)){
  $content = "[".date('d.m.y, h:i:s')."]\n";

  foreach ($array as $value => $key){
  $content.="$value='$key'\n";
  }
  $_content = file_get_contents('hub.foc');
  file_put_contents('hub.foc',"$_content\n$content");
}
?>
