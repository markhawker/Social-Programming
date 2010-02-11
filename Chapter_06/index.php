<?php

include 'config.php';
include 'functions.php';
include 'facebook-platform/php/facebook.php';

$facebook = new Facebook(API_KEY, SECRET);
$official_user = $facebook->get_loggedin_user();

$valid_facebook_session = valid_facebook_session($_COOKIE[API_KEY.'_expires'], $_COOKIE[API_KEY.'_session_key'], $_COOKIE[API_KEY.'_ss'], $_COOKIE[API_KEY.'_user'], $_COOKIE[API_KEY], SECRET);

$unofficial_user = ($valid_facebook_session ? $_COOKIE[API_KEY.'_user'] : false); 

if($official_user) {
 try {
  $app_friends = $facebook->api_client->friends_getAppUsers();
  $unconnected_friends_count = $facebook->api_client->connect_getUnconnectedFriendsCount();
 }
 catch (Exception $e) {
  // There was an exception
 }
} else {
 $unconnected_friends_count = 0;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
 <title>Test Tube</title>
</head>
<body>
 <fb:login-button autologoutlink="true" onlogin="connected();"></fb:login-button>
 <?php echo '<p>User Identifier: '.($official_user ? $official_user : 'Unknown').'</p>'; ?>
 <p>Facebook Name: <span id="facebook_name">Unknown</span></p>
 <p>Facebook Connect Status: <span id="connect_status">Unknown</span></p>
 <?php if($official_user) { ?>
  <p>Connected Friends:</p>
   <ul>
    <?php
    if ($app_friends) {
     foreach($app_friends as $app_friend) {
      echo '<li><fb:name uid="'.$app_friend.'" useyou="false"></fb:name></li>';
     }
    } else {
     echo '<li>You have no friends using the application.</li>';
    }
    ?>
   </ul>
   <p>Unconnected Friends: <fb:unconnected-friends-count></fb:unconnected-friends-count></p>
   <?php if($unconnected_friends_count > 0) { ?>
    <p><a href="#" onclick="FB.Connect.inviteConnectUsers(); return false;">Invite Facebook Friends</a></p>
   <?php } ?>
   <p><a href="disconnect.php">Disconnect Facebook Details</a></p>
  <?php } ?>
  <script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/en_GB" type="text/javascript"></script>
  <script type="text/javascript">
    function connected() {
     document.getElementById('facebook_name').innerHTML = '<fb:name uid="loggedinuser" useyou="false"></fb:name>';
     FB.XFBML.Host.parseDomTree();
    }
    function not_connected() {
     document.getElementById('facebook_name').innerHTML = 'Unknown';
    }
    FB.init('<?php echo API_KEY; ?>', 'xd_receiver.htm', {'reloadIfSessionStateChanged':true, 'ifUserConnected':connected, 'ifUserNotConnected':not_connected});
    FB.ensureInit(function() {
     FB.Connect.get_status().waitUntilReady(function(status) {
      switch (status) {
       case FB.ConnectState.connected:
        document.getElementById('connect_status').innerHTML = 'Connected';
        break;
       case FB.ConnectState.appNotAuthorized:
        document.getElementById('connect_status').innerHTML = 'Not Authorized';
        break;
       case FB.ConnectState.userNotLoggedIn:
        document.getElementById('connect_status').innerHTML = 'Not Logged In';
      }
     });
   });
 </script>
</body>
</html>