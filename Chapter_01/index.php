<?php

require_once 'twitter-async/EpiCurl.php';
require_once 'twitter-async/EpiOAuth.php';
require_once 'twitter-async/EpiTwitter.php';

$username = 'INSERT YOUR TWITTER USERNAME'; // Edit Me
$password = 'INSERT YOUR TWITTER PASSWORD'; // Edit Me
$twitter = new EpiTwitter();

try {
 $response = $twitter->get_basic('/account/verify_credentials.json', null, $username, $password);
 if($response->code == 200) {
   echo '<p>Username: '.$response->screen_name.'</p>';
   echo '<p>Description: '.$response->description.'</p>';
   echo '<h1>All Friends</h1>';
   echo '<ul>';
   $cursor = -1;
   do {
    $friends = $twitter->get_basic('/statuses/friends.json', array('cursor' => $cursor), $username, $password);
    foreach($friends->users as $friend) {
     echo '<li>'.$friend->screen_name.': '.$friend->status->text.'</li>';
    } 
    $cursor = $friends->next_cursor_str;
   } while ($cursor > 0);
   echo '</ul>';
   $twitter->useAsynchronous(true);
   $users = array('INSERT USER 1', 'INSERT USER 2', 'INSERT USER 3'); // Edit Me
   $responses = array();
   foreach($users as $user) {
     $responses[] = $twitter->post_basic('/direct_messages/new.json', array('user' => $user, 'text' => "Hey, {$user}. What’s up?"), $username, $password);
   }
   echo '<h1>Direct Messages</h1>';
   echo '<ul>';
   foreach($responses as $response) {
    echo "<li>Direct Message: {$response->id}</li>";
   }
   echo '</ul>';
 }
}
catch(EpiTwitterException $e){ echo $e->getMessage(); exit; }
catch(Exception $e) { echo $e->getMessage(); exit; }

?>