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
   echo '<h1>User Objects</h1>';
   $user = $twitter->get_usersShow(array('screen_name' => 'markhawker'), $username, $password);
   print_r($user->responseText);
   echo '<h1>Status Objects</h1>';
   $status = $twitter->get_basic('/statuses/show/10525805703.json', null, $username, $password);
   print_r($status->responseText);
   echo '<h1>Direct Message Objects</h1>';
   $direct_messages = $twitter->get_direct_messages(array('count' => 2), $username, $password);
   echo '<ul>';
   foreach($direct_messages as $direct_message) {
     echo '<li>'.$direct_message->text.'</li>';  
   }
   echo '</ul>';
   echo '<h1>Saved Search Objects</h1>';
   $saved_search = $twitter->post_saved_searchesCreate(array('query' => 'test'), $username, $password);
   echo '<p>Saved Search: '.$saved_search->id.'</p>';
   $saved_searches = $twitter->get_saved_searches(null, $username, $password);
   print_r($saved_searches->responseText);
   $delete_saved_search = $twitter->post_basic("/saved_searches/destroy/{$saved_search->id}.json", null, $username, $password);
   echo '<p>Deleted Search: '.$delete_saved_search->id.'</p>';
   echo '<h1>ID Objects</h1>';
   $cursor = -1;
   do {
    $ids = $twitter->get_basic('/friends/ids.json', array('cursor' => $cursor, 'screen_name' => $username), $username, $password);
    foreach($ids->ids as $id) {
     echo '<li>'.$id.'</li>';
    } 
    $cursor = $ids->next_cursor_str;
   } while ($cursor > 0);
   echo '<h1>Relationship Objects</h1>';
   $relationship = $twitter->get_basic('/friendships/show.json', array('target_screen_name' => 'socprog', 'source_screen_name' => $username), $username, $password);
   print_r($relationship->responseText);
   echo '<h1>Response Objects</h1>';
   $friendship = $twitter->get_basic('/friendships/exists.json', array('user_a' => $username, 'user_b' => 'socprog'), $username, $password);
   echo $friendship->responseText;
   echo '<h1>Hash Objects</h1>';
   $rate_limit_status = $twitter->get_basic('/account/rate_limit_status.json', null, $username, $password);
   echo '<p>Remaining Hits: '.$rate_limit_status->remaining_hits.'</p>';
   echo '<p>Hourly Limit: '.$rate_limit_status->hourly_limit.'</p>';
   echo '<p>Reset Time: '.$rate_limit_status->reset_time.'</p>';
   echo '<p>Reset Time in Seconds: '.$rate_limit_status->reset_time_in_seconds.'</p>';
   echo '<h1>Search Objects</h1>';
   $query = 'test';
   $search = $twitter->get_search(array('q' => urlencode($query), 'rpp' => 2), $username, $password);
   echo '<p>Query: '.$search->query.'</p>';
   echo '<ul>';
   foreach($search->results as $result) {
    echo '<li>'.$result->from_user.': '.$result->text.'</li>';
   } 
   echo '</ul>';
   echo '<h1>Trends Objects</h1>';
   echo '<h2>Current Trends</h2>';
   $trends = $twitter->get_trends(null, $username, $password);
   print_r($trends->responseText);
   echo '<h2>Current Trends</h2>';
   $trends_current = $twitter->get_trendsCurrent(array('exclude' => 'hashtags'), $username, $password);
   print_r($trends_current->responseText);
   echo '<h2>Daily Trends</h2>';
   $date = date('Y-m-d');
   $trends_daily = $twitter->get_trendsDaily(array('date' => $date, 'exclude' => 'hashtags'), $username, $password);
   print_r($trends_daily->responseText);
   echo '<h2>Weekly Trends</h2>';
   $trends_weekly = $twitter->get_trendsDaily(array('date' => $date, 'exclude' => 'hashtags'), $username, $password);
   print_r($trends_weekly->responseText);
   echo '<h1>Local Trends Objects</h1>';
   $trends_available = $twitter->get_basic('/trends/available.json', array('lat' => 37, 'long' => -122), $username, $password);
   print_r($trends_available->responseText);
   $trends_location = $twitter->get_basic('/trends/2487956.xml', null, $username, $password);
   print_r($trends_location->headers);
  }
}
catch(EpiTwitterException $e){ echo $e->getMessage(); exit; }
catch(Exception $e) { echo $e->getMessage(); exit; }

?>