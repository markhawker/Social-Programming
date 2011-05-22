<?php

include 'config.php';
include 'facebook-platform/src/facebook.php';

$facebook = new Facebook(array(
  'appId' => APP_ID,
  'secret' => SECRET
));

$file = 'logs/remove.txt';
$file_handler = fopen($file, 'w') or die("Can't open file.");

$signed_request = $facebook->getSignedRequest();

if($signed_request) {
 foreach($signed_request as $key => $value) {
  fwrite($file_handler, $key.': '.$value.', ');
 }
 fwrite($file_handler, "Success.");
} else {
 fwrite($file_handler, "Failure.");
}

fclose($file_handler);

?>