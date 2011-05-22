<?php

include '../config.php';
include '../facebook-platform/src/facebook.php';

$facebook = new Facebook(array(
  'appId' => APP_ID,
  'secret' => SECRET
));

$user = $facebook->getUser();

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
  $_SESSION['state'] = md5(uniqid(rand(), TRUE));
  $redirect_url = "http://www.facebook.com/dialog/oauth?client_id=".APP_ID."&redirect_uri=".urlencode(CANVAS_PAGE_URL)."&state=".$_SESSION['state'];
  echo('<script>top.location.href="'. $redirect_url.'";</script>');
 }

 ?>
 <script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
 <script type="text/javascript">
  FB.init('<?php echo API_KEY; ?>');
 </script>
</body>
</html>