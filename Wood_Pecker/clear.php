<?
$chat_file = $_POST['cf'];
$sel_user = $_POST['su'];
if(!empty($chat_file) && !empty($sel_user)){
  $contact_dir = stristr($chat_file, 'Wood_Pecker/',true).'Wood_Pecker/contacts.foc';
  $contacts_file = file_get_contents($contact_dir);
  $new_c_file = str_replace($sel_user.'=','',$contacts_file);
  file_put_contents($chat_file,'');
  file_put_contents($contact_dir,$new_c_file);
  echo 'true';
}
?>
