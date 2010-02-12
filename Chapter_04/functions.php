<?php

include 'twitter-async/EpiCurl.php';
include 'twitter-async/EpiOAuth.php';
include 'twitter-async/EpiTwitter.php';

define('TWITTER_CONSUMER_KEY', 'INSERT CONSUMER KEY HERE'); // Edit Me
define('TWITTER_CONSUMER_SECRET', 'INSERT CONSUMER SECRET HERE'); // Edit Me
define('INDEX', 'index.php');
define('MASTER', 'master.php');
define('TITLE', 'Test Tube - Sign In With Twitter');

function init($oauth_token = null, $oauth_token_secret = null) {
 return new EpiTwitter(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
}

function login() {
 // An OAuth Token has just been granted from Twitter
 if (!empty($_GET['oauth_token'])) {			
  $twitter = init();
  $oauth_token = $_GET['oauth_token'];
  try {
   $twitter->setToken($oauth_token);
   $token = $twitter->getAccessToken();
   $twitter->setToken($token->oauth_token, $token->oauth_token_secret);
   setcookie('oauth_token', $token->oauth_token);
   setcookie('oauth_token_secret', $token->oauth_token_secret);
   header('Location: '.MASTER.'?loggedin');	
  }
  catch(EpiOAuthException $e) { header('Location: '.INDEX.'?oauthexception'); }
  catch(EpiTwitterException $e) { header('Location: '.INDEX.'?exception'); }
 } else if (empty($_COOKIE['oauth_token']) && empty($_COOKIE['oauth_token_secret'])) {
  setcookie('oauth_token', '', 1);
  setcookie('oauth_token_secret', '', 1);
  header('Location: '.INDEX);	 
 } else {
  return init($_COOKIE['oauth_token'], $_COOKIE['oauth_token_secret']);
 }
}

function logout() {
  $twitter = init($_COOKIE['oauth_token'], $_COOKIE['oauth_token_secret']);
  $twitter->post_accountEnd_session();
  setcookie('oauth_token', '', 1);
  setcookie('oauth_token_secret', '', 1);
  header('Location: '.INDEX.'?loggedout');
}

function verify($twitter) {
 if (is_object($twitter)) {
  $response = $twitter->get_accountVerify_credentials();
  return check($response);
 } else {
  return false;
 }
}

function check($payload) {
 return ($payload->code == 200) ? $payload : false;
}

function printFriends($twitter, $count = 10) {
 try {
  $friends = $twitter->get_statusesFriends(array('cursor' => -1));
  if (check($friends)) {
   $next_cursor = $friends->next_cursor;
   $previous_cursor = $friends->previous_cursor;
   echo '<h2>Latest '.$count.' Twitter Friends</h2>';
   for ($i = 0; $i < $count; $i++) {
    $friend = $friends->users[$i];
    echo '<span><a title="'.$friend->name.'" href="http://twitter.com/'.$friend->screen_name.'"><img class="following" src="'.$friend->profile_image_url.'" alt="'.$friend->screen_name.'" height="48" width="48" /></a>';
   }
  } else {
   return false;
  }
 }
 catch(EpiTwitterException $e) { echo '<p>You have no friends to list.</p>'; }
}

function printRetweets($twitter, $type = "of", $count = 5, $page = 1) {
 try {
  $method = "get_statusesRetweets_".$type."_me";
  $retweets = $twitter->$method(array('count' => $count, 'page' => $page));
  if (check($retweets)) {
   echo '<h2>Latest '.$count.' Retweets '.$type.' Me</h2>';
   echo '<ul>';
   foreach ($retweets as $retweet) {
    echo '<li>'.$retweet->id.': &quot;'.$retweet-> text.'&quot; last retweeted by: ';
    $method = "get_statusesRetweets{$retweet->id}";
    $statuses = $twitter->$method(array('count' => 1));
    if (check($statuses)) {
     $retweeters = '';
     foreach ($statuses as $status) {
      $retweeters .= $status->user->screen_name.', ';
     }
    }
    echo substr($retweeters, 0, -2).'</li>';
   }
   echo '</ul>';
  } else {
   return false;
  }
 }
 catch(EpiTwitterException $e) { echo '<p>You have no retweets to list.</p>'; }
}

function printFollowers($twitter, $count = 10) {
 try {
  $followers = $twitter->get_statusesFollowers(array('cursor' => -1));
  if (check($followers)) {
   $next_cursor = $followers->next_cursor;
   $previous_cursor = $followers->previous_cursor;
   echo '<h2>Latest '.$count.' Twitter Followers</h2>';
   echo '<form name="spam" action="'.MASTER.'" method="post">';
   for ($i = 0; $i < $count; $i++) {
    $follower = $followers->users[$i];
    echo '<span><a title="'.$follower->name.'" href="http://twitter.com/'.$follower->screen_name.'"><img class="follower" src="'.$follower->profile_image_url.'" alt="'.$follower->screen_name.'" height="48" width="48" /></a>';
    echo '<input type="radio" name="spammer" value="'.$follower->screen_name.'" /></span>';
   }
   echo '<input type="hidden" name="method" value="spam" />';
   echo '<p><input type="submit" value="Report Spam" /></p>';
   echo '</form>';
  } else {
   return false;
  }
 }
 catch(EpiTwitterException $e) { echo '<p>You have no followers to list.</p>'; }
}

?>