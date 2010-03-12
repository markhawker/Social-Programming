<?php if ( !defined('BASEPATH')) exit('No direct script access allowed'); 

include 'twitter-async/EpiCurl.php';
include 'twitter-async/EpiOAuth.php';
include 'twitter-async/EpiTwitter.php';

class Twitter {

      function init($oauth_token = null, $oauth_token_secret = null) 
      {
	return new EpiTwitter(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
      }

      function get_url()
      {
	$twitter = $this->init();
	try {
	  return $twitter->getAuthenticateUrl(null, array('force_login' => true));
	}
	catch(EpiOAuthException $e) {
	  return 'oauthexception';
	}
	catch(EpiTwitterException $e) { 
	  return 'twitterexception';
	}
      }

      function verify($twitter) 
      {
	if (is_object($twitter)) {
	  $response = $twitter->get_accountVerify_credentials();
	  return $this->check($response);
	} else {
	  return false;
	}
      }

      function check($payload) 
      {
	return ($payload->code == 200) ? $payload : false;
      }

}

?>