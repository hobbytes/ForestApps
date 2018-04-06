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

    $labels = '';
  if(!empty($array) && !empty($timestamp) && !empty($token) && !empty($user)){
    /* get timezone */
    $timezone = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/system/users/'.$user.'/settings/timezone.foc');
    date_default_timezone_set("$timezone");

    /* check dir */
    $dir = $_SERVER['DOCUMENT_ROOT'].'/system/users/'.$user.'/documents/Remote_Lab/Units/'.$token.'/';
    if(is_dir($dir)){

      $confing = parse_ini_file($dir.'config.foc');//parse config file
      $step = $confing['step'];// get step
      $cstep = $confing['cstep'];// get current $step

      $cfile = file_get_contents($dir.'config.foc');// get config file
    if($step == $cstep){
      $content = "[".date('d.m.y, H:i:s')."]\n";

      foreach ($array as $value => $key){
        $content.="$value='$key'\n";
        if($value!='timestamp'){
          $labels = $labels.','.$value;
        }
      }

      $_content = file_get_contents($dir.'hub.foc');
      file_put_contents($dir.'hub.foc',"$_content\n$content");
      $labels = ltrim($labels,',');// get labels

      if(!preg_match("/labels='$labels'/",$cfile)){//check labels
        $cfile = preg_replace('~^labels=.*$~m',"labels='$labels'",$cfile);
      }

      $cfile = str_replace("cstep='$cstep'","cstep='0'",$cfile);
      file_put_contents($dir.'config.foc',$cfile);
  }else{
    $_cstep = $cstep+1;
    $cfile = str_replace("cstep='$cstep'","cstep='$_cstep'",$cfile);
    file_put_contents($dir.'config.foc',$cfile);
  }
}else{
  die();
}
}
?>
