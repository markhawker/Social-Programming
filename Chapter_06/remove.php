<?php

include 'config.php';
include 'friendlinking.php';
include 'facebook-platform/php/facebook.php';

$facebook = new Facebook(API_KEY, SECRET);

$file = 'logs/remove.txt';
$file_handler = fopen($file, 'w') or die("Can't open file.");

$facebook_parameters = $facebook->get_valid_fb_params($_POST, null, 'fb_sig');

foreach($_POST as $key => $value) {
  fwrite($file_handler, $key.': '.$value.', ');
}

try {
  if (!empty($facebook_parameters) && $facebook->fb_params['uninstall'] == 1) {
    $account_ids = json_decode($facebook->fb_params['linked_account_ids']);
    foreach($account_ids as $account_id) {
      // Lookup E-Mail Hash From Account ID
      $email_addresses[] = hash_email('mark@example.com');
      fwrite($file_handler, $account_id.', ');
    }
    $email_hashes = json_encode($email_addresses);
    $removed_email_hashes = $facebook->api_client->connect_unregisterUsers($email_hashes);
    foreach($removed_email_hashes as $email_hash) {
      fwrite($file_handler, $email_hash.', ');
    }
    fwrite($file_handler, "Success.");
  } else {
    fwrite($file_handler, "Failure.");
  }
}
catch (Exception $e) {
  fwrite($file_handler, "Exception.");
}

fclose($file_handler);

?>