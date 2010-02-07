<?php

include 'config.php';
include 'friendlinking.php';
include 'facebook-platform/php/facebook.php';

$facebook = new Facebook(API_KEY, SECRET);

$file = 'logs/authorize.txt';
$file_handler = fopen($file, 'w') or die("Can't open file.");

$facebook_parameters = $facebook->get_valid_fb_params($_POST, null, 'fb_sig');

foreach($_POST as $key => $value) {
  fwrite($file_handler, $key.': '.$value.', ');
}

try {
  if (!empty($facebook_parameters) && $facebook->fb_params['authorize'] == 1) {
    $email_hashes = $facebook->api_client->users_getInfo($facebook->fb_params['user'], 'email_hashes');
    if ($email_hashes) {
      foreach($email_hashes[0]['email_hashes'] as $email_hash) {
        fwrite($file_handler, $email_hash.', ');
      }
    } else {
      // Create a hash of the user's e-mail address and register with Facebook. This is hard-coded in this example,
      // but could be extracted from a web form upon user registration.
      $email_addresses = array(
        array(
          'email_hash' => hash_email('mark@example.com'),
          'account_id' => 'mark',
          'account_url' => 'http://www.example.com/user/mark'
        )
      );
      $email_hashes = $facebook->api_client->connect_registerUsers(json_encode($email_addresses));
      foreach ($email_hashes as $email_hash) {
        fwrite($file_handler, $email_hash.', ');
      }
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