<?php

include 'config.php';
include 'facebook-platform/php/facebook.php';

$facebook = new Facebook(API_KEY, SECRET);
$user = $facebook->get_loggedin_user();

$logout = $facebook->api_client->auth_revokeAuthorization();

echo $logout;

?>