<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml"> 
<head> 
 <title><?php echo $title; ?> - Sprog</title>
 <link rel="stylesheet" href="<?php echo base_url(); ?>static/style.css" type="text/css" />
</head>
<body>
<div id="header"> 
 <h1 class="title"><img src="<?php echo base_url(); ?>static/logo.png"
 />&nbsp;Sprog&nbsp;<span>a social programming blog</span></h1> 
  <div id="description"> 
   <p><?php echo $title; ?></p> 
  </div>
  <?php if($via) { ?>
  <div id="logged_in_via">
    <p>You are currently logged in via <?php echo $via; ?>.</p>
  </div>
  <?php } ?>
</div>
<div id="wrap">
 <div id="content"> 