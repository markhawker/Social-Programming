<?php

include 'config.php';
include 'facebook-platform/php/facebook.php';

$facebook = new Facebook(API_KEY, SECRET);
$user = $facebook->get_loggedin_user();

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

$file = 'logs/publish.txt';
$file_handler = fopen($file, 'w') or die("Can't open file.");

if ($_POST['method'] == 'publisher_getInterface') {
  $fbml = render_publisher_css();
  $fbml .= render_publisher_js();
  $fbml .= '<div id="self_publish_frame">';
  $fbml .= ' <p>Hello, world.</p>';
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
    'name' => 'Hello, world.',
    'href' => 'http://www.example.com/',
    'caption' => 'This is a test message.',
    'properties' => array()
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