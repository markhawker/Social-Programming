<?php

include 'functions.php';

if (isset($_GET['logout'])) {
 logout();
}
else {
 $twitter = login();
 $user = verify($twitter);
 if ($user) {
  	
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
  <h1>Hello, <?php echo $user->screen_name; ?>!</h1>
  <p><img src="<?php echo $user->profile_image_url; ?>" alt="<?php echo $user->screen_name; ?>" height="48" width="48" /></p>
  <p class="tweet">&quot;<?php echo $user->status->text; ?>&quot;</p>
	
  <?php

  // Print Latest Friends
  printFriends($twitter, 10);
  
  ?>
	
  <p><a href="<?php echo MASTER; ?>?logout">Sign Out</a></p>
	
<?php } else { ?>
	
 <h1>Twitter Error</h1>
 <p>We were unable to verify your Twitter credentials.</p>

<?php } ?>

 </div>
</body>
</html>

<?php } ?>