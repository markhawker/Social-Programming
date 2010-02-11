<?php

include 'config.php';
include 'facebook-platform/php/facebook.php';

$facebook = new Facebook(API_KEY, SECRET);
$user = $facebook->get_loggedin_user();

$disconnect = $facebook->api_client->auth_revokeAuthorization();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
 <title>Test Tube</title>
</head>
<body>
 <h1>User Disconnected</h1>
 <p>Status: <?php echo $disconnect; ?></p>
 <p><a href="index.php">Back Home</a></p>
</body>
</html>