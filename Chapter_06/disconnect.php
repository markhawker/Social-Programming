<?php

include 'config.php';
include 'facebook-platform/php/facebook.php';

$facebook = new Facebook(API_KEY, SECRET);
$user = $facebook->get_loggedin_user();

$disconnect = $facebook->api_client->auth_revokeAuthorization();

header('Location: http://socprog.thebubblejungle.com/facebook/index.php?disconnect='.$disconnect);

?>