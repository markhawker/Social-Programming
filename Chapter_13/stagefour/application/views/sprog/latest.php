<div id="latest">
<h2>Latest Updates</h2>
 
 <?php
 
  foreach($latest_updates as $update) {
 	echo '<div class="update '.$update['source'].'">';
 	echo '<p>'.($update['time'] != -1 ? '<span class="date">'.date("m-d-Y", $update['time']).'</span>' : '&nbsp;').$update['text'].($update['username'] != -1 ? '&nbsp;<span class="username">via '.anchor('sprog/profile/'.$update['username'], $update['username']).'</span>' : '&nbsp;').'</p>';
 	echo '<div class="controls">';
 	if($update['like_count'] != -1) {
 		echo '<span class="like_count">Likes: '.anchor('sprog/like/'.$update['id'], ($update['like_count'] ? $update['like_count'] : 0)).'</span>';
 	}
 	if($update['comment_count'] != -1) {
 		echo '<span class="comment_count">Comments: '.anchor('sprog/view_comment/'.$update['id'], ($update['comment_count'] ? $update['comment_count'] : 0)).'</span>';
 	}
	echo '</div>';
 	echo '</div>'; 
 }
 
 echo $pagination;
 
 ?>
 
</div>

<p><?php echo anchor('sprog/home', 'Home'); ?></p>