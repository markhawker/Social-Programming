<?php

include 'config.php';
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