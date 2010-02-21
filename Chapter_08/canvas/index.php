<?php

include '../config.php';
include '../functions.php';
include '../facebook-platform/php/facebook.php';

$facebook = new Facebook(API_KEY, SECRET);
$user = $facebook->get_loggedin_user();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
 <title>Test Tube</title>
</head>
<body>
 <h1>Canvas Page - Test Tube</h1>
 <?php 

 if ($user) {
  echo '<p>User Identifier: '.($user ? $user : 'Unknown').'</p>';
  echo '<p>Facebook Name: <fb:name uid="'.$user.'" useyou="false"></fb:name></p>';
 }
 else {
  $facebook->require_login();
 }

 ?>
 <script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
 <script type="text/javascript">
  FB.init('<?php echo API_KEY; ?>');
 </script>
</body>
</html>