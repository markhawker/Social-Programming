<?php

include 'config.php';
include 'facebook-platform/src/facebook.php';

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
 <h1>Test Facebook Platform Page</h1>
 <p>User: <?php echo $user; ?></p>

 <?php
 
 if($user) {
  try {
//   $access_token = $facebook->getAccessToken();
//   $friends = $facebook->api(array(
//    'method' => 'fql.query',
//    'query' => 'SELECT uid2 FROM friend WHERE uid1="'.$user.'" LIMIT 10'
//   ));
//   echo '<h2>Friends - FQL</h2>';
//   foreach($friends as $friend) {
//    echo '<p><fb:name uid="'.$friend['uid2'].'" /></p>';
//   }
//   $friends_json = file_get_contents('https://graph.facebook.com/me/friends?access_token='.$access_token);  
//   $friends = json_decode($friends_json);
//   echo '<h2>Friends - Facebook API</h2>';
//   foreach($friends->data as $friend) {
//    echo '<p><fb:name uid="'.$friend->id.'" /></p>';
//   }
   echo '<h2>Group Members</h2>';
   $queries = array(
    'group_members' => 'SELECT uid, positions FROM group_member WHERE gid="2205007948" LIMIT 5', 
    'members_details' => 'SELECT id, name, url, pic FROM profile WHERE id IN (SELECT uid FROM #group_members)'
   );
   $queries = json_encode($queries);
   $data = $facebook->api(array(
    'method' => 'fql.multiquery',
    'queries' => $queries
   ));
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