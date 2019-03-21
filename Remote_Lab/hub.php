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

  if(!empty($array) && !empty($token) && !empty($user)){
    /* get timezone */
    $timezone = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/system/users/'.$user.'/settings/timezone.foc');
    date_default_timezone_set("$timezone");

    /* if empty timestamp, then generate timestamp and push in array */
    if(empty($timestamp)){
      $_date = new DateTime();
      $timestamp = $_date->getTimestamp();
      $array['timestamp'] = $timestamp;
    }

    /* check dir */
    $dir = $_SERVER['DOCUMENT_ROOT'].'/system/users/'.$user.'/documents/Remote_Lab/Units/'.$token.'/';
    if(is_dir($dir)){

      $confing = parse_ini_file($dir.'config.foc');//parse config file
      $step = $confing['step']; // get step
      $cstep = $confing['cstep']; // get current $step
      $uname = mb_strtoupper($confing['name']); // get unit name

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

          $op1 = $value['operand1'];

          if($condition){

            //send emeail
            $email = $value['email'];

            if(filter_var($email, FILTER_VALIDATE_EMAIL) || !empty($value['request'])){

              $prefix_email = NULL;

              /* get localization file */
              $get_locale = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/system/users/'.$user.'/settings/language.foc');
              $localization = parse_ini_file('assets/Lang/'.$get_locale.'.lang');

              if($value['selfd'] == 'true'){
                $prefix_email = $localization['prefix_email'];
              }

              $fuid = $bd->readglobal2("fuid", "forestusers", "login", $user, true);
              $mt = $localization['hub_mt']." $uname";
              $mb = $localization['hub_mb_1']."<span style='background-color:#03A9F4; color:#FFF; padding: 1px; border-radius: 5px;'>$uname - '<b>$condition_title</b>'; [$op1 = $find]</span> ".$localization['hub_mb_2']." $prefix_email";
              $data = http_build_query(array('fuid' => $fuid, 'mt' => $mt, 'mb' => $mb, 'mr' => $email, 'hash' => md5(date('dmyhis').$email.$mt.$mb.$mr.$fuid)));
              $check = file_get_contents('http://forest.hobbytes.com/media/os/modules/EmailSender.php?'.$data);
              echo $check;
            }else{
              echo "$email - wrong email!";
            }

            //make request
            if(!empty($value['request'])){
              $url = $value['request'];
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_HEADER, false);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              $output = curl_exec($ch);
              curl_close($ch);
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

    $cfile = file_get_contents($dir.'config.foc'); // get config file
    if($step == $cstep){
      $content = "[".date('d.m.y, H:i:s')."]\n";

      //print_r($array); //ПОСМОТРИ ЧТО ТУТ ВЫХОДИТ!

      foreach ($array as $value => $key){
        $content.="$value='$key'\n";
        if($value != 'timestamp'){
          $labels .= $value.',';
        }
      }

      $labels_part = explode(',', mb_substr($labels, 0, -1));

      $newlabels = "";

      foreach($labels_part as $part){
        if(!preg_match("%$part%", $confing['labels'])){ //&& !preg_match("%$part%", $labels)
          $newlabels .= $part.',';
        }
      }

      $newlabels = mb_substr($newlabels, 0, -1);

      if(!empty($newlabels)){
        $labels = $confing['labels'].','.$newlabels;
      }else{
        $labels = $confing['labels'];
      }



      $_content = file_get_contents($dir.'hub.foc');
      file_put_contents($dir.'hub.foc', "$_content\n$content");
      $labels = ltrim($labels, ',');// get labels

      if(!preg_match("/labels='$labels'/", $cfile)){ //check labels
        $cfile = preg_replace('~^labels=.*$~m', "labels='$labels'", $cfile);
      }

      $cfile = str_replace("cstep='$cstep'","cstep='0'",$cfile);
      file_put_contents($dir.'config.foc', $cfile);

  }else{
    $_cstep = $cstep + 1;
    $cfile = str_replace("cstep='$cstep'", "cstep='$_cstep'", $cfile);
    file_put_contents($dir.'config.foc', $cfile);
  }
}else{
  die();
}
}
?>
