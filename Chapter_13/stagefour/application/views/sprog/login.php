<div id="login">

<?php

if(!empty($error)) { echo '<p class="error">'.$error.'</p>'; }
if(!empty($has_twitter)) { echo '<p class="twitter_message">'.$has_twitter.'</p>'; }
if(!empty($has_facebook)) { echo '<p class="facebook_message">'.$has_facebook.'</p>'; }
  
echo form_open('sprog/login');
echo form_label('User Name', 'username');
echo form_input('username');
echo form_label('Password', 'password');
echo form_password('password');
echo form_submit('submit', 'Login');
echo anchor('sprog/register', 'Create an Account');
echo form_close();

if(empty($has_twitter) && empty($has_facebook)) {
 echo '<h2>Alternative Logins</h2>';
 echo '<p><a href="'.$twitter_url.'"><img src="'.base_url().'static/siwt-darker.png" height="24" width="151" alt="Sign In With Twitter" /></a>&nbsp;<fb:login-button></fb:login-button></p>';
}

?>
</div>