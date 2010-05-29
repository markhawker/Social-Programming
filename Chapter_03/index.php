<?php

include 'functions.php';

$twitter = init();

try {
 $authorize_url = $twitter->getAuthorizeUrl();
 $authenticate_url_forced = $twitter->getAuthenticateUrl(null, array('force_login' => true));
 $authenticate_url_unforced = $twitter->getAuthenticateUrl();
}
catch(EpiOAuthException $e) { 
 echo '<p>There was an OAuth exception.</p>';
 echo '<ul><li>Check that you have entered a valid Consumer Key and Consumer Secret.</li><li>Check that your PHP version is 5.2+ which supports multicurl.</li><li>Check that you have the current version of the <a href="http://github.com/jmathai/twitter-async">twitter-async library</a>.</li></ul>';
 echo '<p>*** STACK TRACE ***</p>'; 
 print_r($e); 
 echo '<p>*** END OF STACK TRACE ***</p>';
 exit;
}
catch(EpiTwitterException $e) { 
 echo '<p>There was an unknown exception.</p>';
 echo '<p>*** STACK TRACE ***</p>'; 
 print_r($e); 
 echo '<p>*** END OF STACK TRACE ***</p>';
 exit;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <title><?php echo TITLE; ?></title>
 <link href="static/css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
 <div id="main">
  <h1>Test Tube</h1>
  <p>This application uses Twitter's "Sign In With Twitter" feature to demonstrate what is possible in only a few lines of code.</p>
  <p id="authorize"><a href="<?php echo $authorize_url; ?>">Authorize With Twitter</a></p>
  <h2>Forced Login</h2>
  <p>Whether a User is logged into Twitter or not they will be prompted to login and then Allow/Deny the application.</p>
  <p id="authenticate"><a href="<?php echo $authenticate_url_forced; ?>"><img src="static/img/siwt-darker.png" height="24" width="151" alt="Sign In With Twitter" /></a></p>
  <h2>Unforced Login</h2>
  <p>The currently logged in User will be used and then prompted to then Allow/Deny the application.</p>
  <p id="authenticate"><a href="<?php echo $authenticate_url_unforced; ?>"><img src="static/img/siwt-darker.png" height="24" width="151" alt="Sign In With Twitter" /></a></p>
 </div>
</body>
</html>