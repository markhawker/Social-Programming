<div id="login">

<?php

if(!empty($error)) { echo '<p class="error">'.$error.'</p>'; }
  
echo form_open('sprog/login');
echo form_label('User Name', 'username');
echo form_input('username');
echo form_label('Password', 'password');
echo form_password('password');
echo form_submit('submit', 'Log In');
echo anchor('sprog/register', 'Create an Account');
echo form_close();

?>
</div>