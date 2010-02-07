<?php

include 'config.php';
include 'functions.php';
include 'facebook-platform/php/facebook.php';

$facebook = new Facebook(API_KEY, SECRET);
$official_user = $facebook->get_loggedin_user();

$valid_facebook_session = valid_facebook_session($_COOKIE[API_KEY.'_expires'], $_COOKIE[API_KEY.'_session_key'], $_COOKIE[API_KEY.'_ss'], $_COOKIE[API_KEY.'_user'], $_COOKIE[API_KEY], SECRET);

$unofficial_user = ($valid_facebook_session ? $_COOKIE[API_KEY.'_user'] : false); 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
 <title>Test Tube</title>
</head>
<body>
 <h1>Test Facebook Connect Page</h1>
 <p>Official Client User: <?php echo $official_user; ?></p>
 <p>Unofficial Client User: <?php echo $unofficial_user; ?></p>

 <?php
 
 if($official_user) {
  try {
//   $fql = "SELECT uid2 FROM friend WHERE uid1=".$official_user." LIMIT 10";
//   $friends_fql = $facebook->api_client->fql_query($fql);
//   echo '<h2>Friends - FQL</h2>';
//   foreach($friends_fql as $friend) {
//    echo '<p><fb:name uid="'.$friend['uid2'].'" /></p>';
//   }
//   $friends = $facebook->api_client->friends_get();
//   echo '<h2>Friends - Facebook API</h2>';
//   foreach($friends as $friend) {
//   echo '<p><fb:name uid="'.$friend.'" /></p>';
//   }
   echo '<h2>Group Members</h2>';
   $queries = array(
    'group_members' => 'SELECT uid, positions FROM group_member WHERE gid="2205007948" LIMIT 5', 
    'members_details' => 'SELECT id, name, url, pic FROM profile WHERE id IN (SELECT uid FROM #group_members)'
   );
   $queries = json_encode($queries);
   $data = $facebook->api_client->fql_multiquery($queries);
   $group_members = $data[0]['fql_result_set'];
   $members_details = $data[1]['fql_result_set'];
   $i = 0;
   foreach($group_members as $group_member) {
    echo '<fb:name uid="'.$group_member['uid'].'"></fb:name>&nbsp;'.$members_details[$i]['name'].'<br />';
    $i++;
   }
  }
  catch(Exception $e) {
   print_r($e);
  }
 } else {
  echo '<p>User not logged in.</p>';
 }

 ?>

 <script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
 <fb:login-button autologoutlink="true" onlogin="login();"></fb:login-button>
 <script type="text/javascript">
  FB.init("<?php echo API_KEY; ?>", "xd_receiver.htm", {'reloadIfSessionStateChanged':true});
  function login() {
   alert('Logged into Facebook.');
  }
 </script>
</body>
</html>