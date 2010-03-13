<?php if($is_google) { ?>
  <div id="google_comments" style="width: 610px; border: 1px solid #ccc;"></div>
<?php } else { ?>

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

<? } ?>

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
 <script type="text/javascript">
 var skin = {};
  skin['BORDER_COLOR'] = '#cccccc';
  skin['ENDCAP_BG_COLOR'] = '#e0ecff';
  skin['ENDCAP_TEXT_COLOR'] = '#333333';
  skin['ENDCAP_LINK_COLOR'] = '#0000cc';
  skin['ALTERNATE_BG_COLOR'] = '#ffffff';
  skin['CONTENT_BG_COLOR'] = '#ffffff';
  skin['CONTENT_LINK_COLOR'] = '#0000cc';
  skin['CONTENT_TEXT_COLOR'] = '#333333';
  skin['CONTENT_SECONDARY_LINK_COLOR'] = '#7777cc';
  skin['CONTENT_SECONDARY_TEXT_COLOR'] = '#666666';
  skin['CONTENT_HEADLINE_COLOR'] = '#333333';
  skin['DEFAULT_COMMENT_TEXT'] = '- add your comment here -';
  skin['HEADER_TEXT'] = 'Comments';
  skin['POSTS_PER_PAGE'] = '5';
  google.friendconnect.container.renderWallGadget({ 
    id: 'google_comments',
    site: '<?php echo GFC_SITE_ID; ?>',
    'view-params':{"disableMinMax":"true","scope":"PAGE","features":"video,comment","startMaximized":"true"}
  }, skin);
</script>
</div>