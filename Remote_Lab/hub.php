<?
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: " . date("r"));
clearstatcache();
if(isset($_GET)){
  $array = $_GET;
  $timestamp = $_GET['timestamp'];
  $user = $_GET['user'];
  $token = $_GET['token'];
}else{
  $array = $_POST;
  $timestamp = $_POST['timestamp'];
  $user = $_POST['user'];
  $token = $_POST['token'];
}

unset($array['user']);
unset($array['token']);

$confing = parse_ini_file('config.foc');
$step = $confing['step'];// get step
$cstep = $confing['cstep'];// get current $step
    $labels = '';
  if(!empty($array) && !empty($timestamp) && !empty($token) && !empty($user)){
    $dir = $_SERVER['DOCUMENT_ROOT'].'/system/users/'.$user.'/documents/Remote_Lab/Units/'.$token.'/';
    if(is_dir($dir)){

    if($step == $cstep){
      $content = "[".date('d.m.y, h:i:s')."]\n";
      foreach ($array as $value => $key){
        $content.="$value='$key'\n";
        $labels = $labels.','.$value;
      }
      $_content = file_get_contents($dir.'hub.foc');
      file_put_contents($dir.'hub.foc',"$_content\n$content");
      $cfile = file_get_contents($dir.'config.foc');
      $labels = ltrim($labels,',');// get labels
      if(!preg_match("/labels='$labels'/",$cfile)){//check labels
        $cfile = preg_replace('~^labels=.*$~m',"labels='$labels'",$cfile);
      }
      $cfile = str_replace("cstep='$cstep'","cstep='0'",$cfile);
      file_put_contents($dir.'config.foc',$cfile);
  }else{
    $_cstep = $cstep+1;
    $cfile = file_get_contents($dir.'config.foc');
    $cfile = str_replace("cstep='$cstep'","cstep='$_cstep'",$cfile);
    file_put_contents($dir.'config.foc',$cfile);
  }
}else{
  die();
}
}
?>
