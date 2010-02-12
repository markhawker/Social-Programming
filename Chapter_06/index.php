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
  $locale = $facebook->api_client->fql_query('SELECT locale FROM user WHERE uid = "'.$official_user.'"');
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
 <fb:window-title style="display: none;">
  <fb:intl desc="Title of the Web page.">Test Tube</fb:intl>
 </fb:window-title>
 <fb:login-button autologoutlink="true" onlogin="connected();"></fb:login-button>
 <?php echo '<p><fb:intl desc="Label for user identifier.">User Identifier:</fb:intl> '.($official_user ? $official_user : '<fb:intl desc="Label for unknown data.">Unknown</fb:intl>').'</p>'; ?>
<p><fb:intl desc="Label for Facebook name.">Facebook Name:</fb:intl> <span id="facebook_name"><fb:intl desc="Label for unknown data.">Unknown</fb:intl></span></p>
   <p><fb:intl desc="Label for Facebook Connect status.">Facebook Connect Status:</fb:intl> <span id="connect_status"><fb:intl desc="Label for unknown data.">Unknown</fb:intl></span></p>
 <?php if($official_user) { ?>
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
   <p><fb:intl desc="Label for unconnected friends.">Unconnected Friends:</fb:intl> <fb:unconnected-friends-count></fb:unconnected-friends-count></p>
   <?php if($unconnected_friends_count > 0) { ?>
    <p><a href="#" onclick="FB.Connect.inviteConnectUsers(); return false;"><fb:intl desc="Link to invite Facebook friends.">Invite Facebook Friends</fb:intl></a></p>
   <?php } ?>
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