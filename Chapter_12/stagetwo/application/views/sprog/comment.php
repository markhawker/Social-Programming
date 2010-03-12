<div id="comment">
 
 	<?php
	
	echo validation_errors('<p class="error">');
	echo form_open('sprog/comment');
	echo form_label('Comment?', 'comment', array('style' => 'font-size: 2em;'));
	echo form_input(array('name' => 'comment'));
	echo form_hidden('update_id', $this->uri->segment(3));
	echo form_submit('submit', 'Comment');
	echo anchor('sprog/home', 'Cancel');
	echo form_close();
	
	?>
 
</div>

<div id="latest">
 <h2>Original</h2>
 	<div class="update <?php echo $original['source']; ?>"><?php echo $original['text'].'&nbsp;<span class="username">via '.anchor('sprog/profile/'.$original['username'], $original['username']).'</span>'; ?></div>
 <h2>Comments</h2>
 <?php 
 
 foreach($latest_comments as $comment) {
 	echo '<div class="comment '.$comment['source'].'">';
 	echo '<p>'.($comment['time'] != -1 ? '<span class="date">'.date("m-d-Y", $comment['time']).'</span>' : '&nbsp;').$comment['text'].($comment['username'] != -1 ? '&nbsp;<span class="username">via '.anchor('sprog/profile/'.$comment['username'], $comment['username']).'</span>' : '&nbsp;').'</p>';
	echo '</div>';
 }
 
 echo $pagination;
 
 ?>
</div>