<?php if ( !defined('BASEPATH')) exit('No direct script access allowed'); 

include 'facebook-platform/php/facebook.php';

class Facebook_Library {

  function get_facebook()
  {
    return new Facebook(API_KEY, SECRET);
  }

}

?>