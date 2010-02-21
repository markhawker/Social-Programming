<?php

include '../config.php';
include '../functions.php';
include '../facebook-platform/php/facebook.php';

$facebook = new Facebook(API_KEY, SECRET);

$facebook_parameters = $facebook->get_valid_fb_params($_POST, null, 'fb_sig');

if(empty($facebook_parameters)) {
 $facebook->redirect(CANVAS_PAGE_URL.'/');
 exit;
}

if($facebook_parameters['is_ajax'] == 1) {
 $owner = $_POST['owner']; 
} else {
 $owner = $facebook_parameters['profile'];
}

$viewer = $facebook_parameters['user'];
$comment_text = $_POST['comment_text'];
$publish_comment = $_POST['publish_comment'];

$facebook->set_user($viewer, $facebook_parameters['session_key']);

$json = array();

$json['fbml_comments'] = '<p>The page <a href="http://www.facebook.com/profile.php?id='.$owner.'&v=app_'.$facebook_parameters['app_id'].'">must be refreshed</a> to view recently-submitted comments.</p>';

try {
 $title = 'Test Tube';
 $url = CANVAS_PAGE_URL;
 $comment = $facebook->api_client->comments_add('c_'.$owner, $comment_text, $viewer, $title, $url, $publish_comment);
 $json['fbml_response'] = '<p><fb:success message="Your comment was added and will be viewable the next time you visit this tab." /></p>';
}
catch(Exception $e) {
 $json['fbml_response'] = '<p><fb:error message="'.$e->getMessage().'" /></p>';
}

echo json_encode($json);

?>