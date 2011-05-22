<?php

include 'config.php';
include 'facebook-platform/src/facebook.php';

$facebook = new Facebook(array(
  'appId' => APP_ID,
  'secret' => SECRET
));

$user = $facebook->getUser();

if($user) {
 try {
  $app_friends = $facebook->api(array(
   'method' => 'friends.getAppUsers'
  ));
  $locale = $facebook->api(array(
   'method' => 'fql.query',
   'query' => 'SELECT locale FROM user WHERE uid = "'.$user.'"'
  ));
 }
 catch (Exception $e) {
  $user = null;
 }
}

if ($user) {
  $logout_url = $facebook->getLogoutUrl();
} else {
  $login_url = $facebook->getLoginUrl();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
 <title>Test Tube</title>
</head>
<body>
 <fb:window-title style="display: none;">
  <fb:intl desc="Title of the Web page.">Test Tube</fb:intl>
 </fb:window-title>
 <?php if ($user): ?>
  <p><a href="<?php echo $logout_url; ?>"><img src="http://static.ak.fbcdn.net/rsrc.php/v1/yf/r/C9lMHpC5ik8.gif" alt="Logout" /></a></p>
 <?php else: ?>
  <p><a href="<?php echo $login_url; ?>"><img src="http://static.ak.fbcdn.net/rsrc.php/v1/yq/r/RwaZQIP0ALn.gif" alt="Connect with Facebook" /></a></p>
 <?php endif ?>
 <?php if($user) { ?>
   <p><fb:intl desc="Label for user identifier.">User Identifier:</fb:intl> <?php echo ($user ? $user : '<fb:intl desc="Label for unknown data.">Unknown</fb:intl>'); ?></p>
   <p><fb:intl desc="Label for Facebook name.">Facebook Name:</fb:intl> <span id="facebook_name"><fb:intl desc="Label for unknown data.">Unknown</fb:intl></span></p>
   <p><fb:intl desc="Label for Facebook Connect status.">Facebook Connect Status:</fb:intl> <span id="connect_status"><fb:intl desc="Label for unknown data.">Unknown</fb:intl></span></p>
   <p><fb:intl desc="Label for connected friends.">Connected Friends:</fb:intl></p>
   <ul>
    <?php
    if ($app_friends) {
     foreach($app_friends as $app_friend) {
      echo '<li><fb:name uid="'.$app_friend.'" useyou="false"></fb:name></li>';
     }
    } else {
     echo '<li><fb:intl desc="Label if no friends are using the application.">You have no friends using the application.</fb:intl></li>';
    }
    ?>
   </ul>
   <p><a href="disconnect.php"><fb:intl desc="Link to disconnect Facebook details.">Disconnect Facebook Details</fb:intl></a></p>
  <?php } ?>
  <script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/<?php echo $locale[0]['locale']; ?>" type="text/javascript"></script>
  <script type="text/javascript">
    function connected() {
     document.getElementById('facebook_name').innerHTML = '<fb:name uid="loggedinuser" useyou="false"></fb:name>';
     FB.XFBML.Host.parseDomTree();
    }
    function not_connected() {
     document.getElementById('facebook_name').innerHTML = '<fb:intl desc="Label for unknown data.">Unknown</fb:intl>';
    }
    FB.init('<?php echo API_KEY; ?>', 'xd_receiver.htm', {'reloadIfSessionStateChanged':true, 'ifUserConnected':connected, 'ifUserNotConnected':not_connected});
    FB.ensureInit(function() {
     FB.Connect.get_status().waitUntilReady(function(status) {
      switch (status) {
       case FB.ConnectState.connected:
        document.getElementById('connect_status').innerHTML = '<fb:intl desc="Label for connected user.">Connected</fb:intl>';
        break;
       case FB.ConnectState.appNotAuthorized:
        document.getElementById('connect_status').innerHTML = '<fb:intl desc="Label for not authorized user.">Not Authorized</fb:intl>';
        break;
       case FB.ConnectState.userNotLoggedIn:
        document.getElementById('connect_status').innerHTML = '<fb:intl desc="Label for not logged in user.">Not Logged In</fb:intl>';
      }
     });
   });
 </script>
</body>
</html>