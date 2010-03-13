<?php if ( !defined('BASEPATH')) exit('No direct script access allowed'); 

include 'osapi/osapi.php';

class Google {

  function get_google_oauth($userId)
  {
  	$provider = new osapiFriendConnectProvider();
	$authentication = new osapiOAuth2Legged(GFC_CONSUMER_KEY, GFC_CONSUMER_SECRET, $userId);
	return new osapi($provider, $authentication);
  }
  
  function get_google_cookie($cookie)
  {
    $provider = new osapiFriendConnectProvider();
    $authentication = new osapiFCAuth($cookie);
    return new osapi($provider, $authentication);
  }

}

?>