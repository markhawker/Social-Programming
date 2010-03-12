<div id="update">
	
   <?php
	
   echo validation_errors('<p class="error">');
	
   echo form_open('sprog/update');
   echo form_label('Update Me?', 'update', array('style' => 'font-size: 2em;'));
   echo form_input(array('name' => 'update'));
   if($has_twitter) {
     echo form_label('Post to Twitter?', 'twitter');
     echo form_checkbox('twitter', 1, true);
     echo '<br /><br />';
   }
   echo form_submit('submit', 'Update');
   echo form_close();
	
   ?>
	
</div>

<div id="latest">
 <h2>My Updates</h2>
 <?php 
 
 foreach($updates as $update) {
     echo '<div class="update '.$update['source'].'">';
     echo '<p>'.($update['time'] != -1 ? '<span class="date">'.date("m-d-Y", $update['time']).'</span>' : '&nbsp;').$update['text'].'</p>';
    echo '<div class="controls">';
    if($update['like_count'] != -1) {
      echo '<span class="like_count">Likes: '.($update['like_count'] ? $update['like_count'] : 0).'</span>';
    }
    if($update['comment_count'] != -1) {
      echo '<span class="comment_count">Comments: '.($update['comment_count'] ? $update['comment_count'] : 0).'</span>';
    }
    if($update['id'] != -1) {
      echo '<span class="delete">'.anchor('sprog/delete/'.$update['id'], 'Delete').'</span>';
    }
    echo '</div>';
    echo '</div>'; 
 }
 
 echo $pagination;
 
 ?>
 
 <h2>My Latest Comments</h2>
 <?php
 
 foreach($comments as $comment) {
   echo '<div class="update '.$comment['source'].'">';
   echo '<p>'.($comment['time'] != -1 ? '<span class="date">'.date("m-d-Y", $comment['time']).'</span>' : '&nbsp;').($comment['id'] != -1 ? anchor('sprog/view_comment/'.$comment['id'], $comment['text']) : $comment['text']).'</p>';
   echo '</div>'; 
 }
 
 ?>
 
</div>

<p><?php echo anchor('sprog/profile/'.$username, 'My Profile'); ?> | <?php echo anchor('sprog/latest', 'Latest Updates'); ?> | <?php echo anchor('sprog/logout', 'Logout'); ?></p>