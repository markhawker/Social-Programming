<div id="register">

<?php

if(!empty($error)) { echo '<p class="error">'.$error.'</p>'; }
	
echo validation_errors('<p class="error">');

echo form_open('sprog/create');
echo form_label('User Name', 'username');
echo form_input(array('name' => 'username', 'value' => set_value('username')));
echo form_label('Full Name', 'fullname');
echo form_input(array('name' => 'fullname', 'value' => set_value('fullname')));
echo form_label('Password', 'password');
echo form_password(array('name' => 'password', 'value' => set_value('password')));
echo form_label('Confirm Password', 'confirm_password');
echo form_password(array('name' => 'confirm_password', 'value' => set_value('confirm_password')));
echo form_submit('submit', 'Create Account');
echo anchor('sprog/index', 'Cancel');
echo form_close();
	
?>

</div>