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

      $conditions_file = $dir.'conditions.foc';
      if(is_file($conditions_file)){
        $conditions = parse_ini_file($conditions_file, true);
        require_once $_SERVER['DOCUMENT_ROOT'].'/system/core/library/bd.php';
        $bd = new readbd;
      }

      foreach ($conditions as $key => $value) {
        $find = $value['operand1'];
        $find = $_GET["$find"];
        if(!empty($find)){
          $condition_title = $value['operand1'].$value['condition'].$value['operand2'];
          $op = 'return '.$find.$value['condition'].$value['operand2'].";";
          $condition = eval("$op");

          if($condition){

            //send emeail
            $email = $value['email'];

            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
              $fuid = $bd->readglobal2("fuid", "forestusers", "login", $user, true);
              $mt = "Remote Lab. Condition ($condition_title)";
              $mb = "Hello from Remote Lab! Your condition '<b>$condition_title</b>' is fuldiled";

              $data = http_build_query(array('fuid' => $fuid, 'mt' => $mt, 'mb' => $mb, 'mr' => $email, 'hash' => md5(date('dmyhis').$email.$mt.$mb.$mr.$fuid)));
              $check = file_get_contents('http://forest.hobbytes.com/media/os/modules/EmailSender.php?'.$data);
              echo $check;
            }else{
              echo "$email - wrong email!";
            }
            //delete if self-destruct
            if($value['selfd'] == 'true'){
              $file = file_get_contents($conditions_file);
        			$update_condition = preg_replace("%(?ms)^\[$key](?:(?!^\[[^]\r\n]+]).)*%", '', $file);
        			file_put_contents($conditions_file, $update_condition);
            }

          }

        }
      }

      $confing = parse_ini_file($dir.'config.foc');//parse config file
      $step = $confing['step'];// get step
      $cstep = $confing['cstep'];// get current $step

      $cfile = file_get_contents($dir.'config.foc');// get config file
    if($step == $cstep){
      $content = "[".date('d.m.y, H:i:s')."]\n";

      foreach ($array as $value => $key){
        $content.="$value='$key'\n";
        if($value != 'timestamp'){
          $labels = $labels.','.$value;
        }
      }

      $labels_part = explode(',', $labels);

      foreach($labels_part as $part){
        if(!preg_match("%$part%", $confing['labels']) && !preg_match("%$part%", $labels)){
          $labels =  $confing['labels'].','.$part;
        }else{
          $labels = $confing['labels'];
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
