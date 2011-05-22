<?php

include 'config.php';
include 'facebook-platform/src/facebook.php';

$facebook = new Facebook(array(
  'appId' => APP_ID,
  'secret' => SECRET
));

$user = $facebook->getUser();

function render_publisher_css() {
  return '
  <style type="text/css">
    #self_publish_frame { padding: 10px; }
  </style>
  ';
}

function render_publisher_js() {
  return '
  <script type="text/javascript">
    function enable_publish() {
      Facebook.setPublishStatus(true);
    }
  </script>
  ';
}

function error_and_exit($error_title, $error_message) {
  $data = array(
    'errorCode' => 1,
    'errorTitle' => $error_title,
    'errorMessage' => $error_message
  );
  echo json_encode($data);
  fwrite($file_handler, 'Error.');
  exit;
}

$file = 'logs/self_publish.txt';
$file_handler = fopen($file, 'w') or die("Can't open file.");

if ($_POST['method'] == 'publisher_getInterface') {
  $fbml = render_publisher_css();
  $fbml .= render_publisher_js();
  $fbml .= '<div id="self_publish_frame">';
  $fbml .= ' <form>';
  $fbml .= '  <label for="mood">How are you feeling today?</label><br /><br />';
  $fbml .= '  <select name="mood" onclick="enable_publish(); return false;">';
  $fbml .= '   <option value="undecided">Undecided</option>';
  $fbml .= '   <option value="happy">Happy</option>';
  $fbml .= '   <option value="sad">Sad</option>';
  $fbml .= '  </select>';
  $fbml .= ' </form>';
  $fbml .= '</div>';
  $content = array(
    'fbml' => $fbml,
    'publishEnabled' => false,
    'commentEnabled' => true
  );
}
else if ($_POST['method'] == 'publisher_getFeedStory') {
  $comment = $_POST['comment_text'];
  $attachment = array(
    'name' => 'I\'ve just updated my mood.',
    'href' => 'http://www.example.com/',
    'caption' => 'Today, {*actor*} is feeling '.$_POST['app_params']['mood'].'.',
    'properties' => array(
      'mood' => $_POST['app_params']['mood']
    )
  );
  $content = array('attachment' => $attachment);
}
else {
  error_and_exit('Error', 'Unknown method.');
}
// Send response back to Facebook
$data = array('method' => $_POST['method'], 'content' => $content);
echo json_encode($data);

fwrite($file_handler, json_encode($data));

fclose($file_handler);

?>