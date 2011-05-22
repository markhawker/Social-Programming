<?php

include 'config.php';
include 'facebook-platform/src/facebook.php';

$facebook = new Facebook(array(
  'appId' => APP_ID,
  'secret' => SECRET
));

$user = $facebook->getUser();

$example_1 = array(
 'name' => 'Facebook',
 'href' => 'http://www.facebook.com/',
 'description' => 'Facebook Home Page.',
 'media' => array(
   array(
     'type' => 'image',
     'src' => 'http://static.ak.facebook.com/images/wiki_logo.png',
     'href' => 'http://www.facebook.com/'
    )
 )
);

$example_2 = array(
 'name' => 'Facebook Song',
 'href' => 'http://www.youtube.com/watch?v=rSnXE2791yg',
 'description' => 'Rhett and Link\'s Facebook Song.',
 'media' => array(
  array(
   'type' => 'flash',
   'swfsrc' => 'http://www.youtube.com/v/rSnXE2791yg',
   'imgsrc' => 'http://i3.ytimg.com/i/bochVIwBCzJb9I2lLGXGjQ/1.jpg?v=776716',
   'width' => 130,
   'height' => 110,
   'expanded_width' => 320,
   'expanded_height' => 265
  )
 )
);

$example_1_json = json_encode($example_1);
$example_2_json = json_encode($example_2);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
  <title>Test Tube</title>
</head>
<body>
  <fb:login-button v="2" autologoutlink="true"></fb:login-button>
  <?php
  if($user) { 
    $access_token = $facebook->getAccessToken();
    try {
      $extended_permissions = $facebook->api(array(
       'method' => 'fql.query',
       'query' => 'SELECT uid, publish_stream, read_stream FROM permissions WHERE uid = "'.$user.'"'
      ));
      $page_administrations = $facebook->api(array(
       'method' => 'fql.query',
       'query' => 'SELECT uid, page_id, type FROM page_admin WHERE uid = "'.$user.'"'
      ));
      $shares = $facebook->api(array(
       'method' => 'fql.query',
       'query' => 'SELECT normalized_url, share_count, like_count, comment_count, total_count, click_count FROM link_stat WHERE url IN ("facebook.com", "google.com")'
      ));
      $stream_json = file_get_contents('https://graph.facebook.com/me/home?access_token='.$access_token);  
      $stream = json_decode($stream_json);
    }
    catch (Exception $e) {
      print_r($e);
    }
    ?>
    <p>User: <?php echo $user; ?></p>
    <p><fb:bookmark type="off-facebook"></fb:bookmark></p>
    <?php
    echo '<h1>Shares</h1>';
    print_r($shares);
    echo '<h1>Comments</h1>';
    echo '<fb:comments xid="test"></fb:comments>';
    echo '<h1>Extended Permissions</h1>';
    print_r($extended_permissions);
    echo '<h1>Page Administrators</h1>';
    print_r($page_administrations);
    echo '<h1>Stream Publishing</h1>';
    print_r($stream->data);
    ?>
    <p><a href="#" onclick="get_permissions();" />Request Read and Write Permissions</a></p>
    <p><a href="#" onclick="get_write_permission();" />Get Write Permission and Post to Feed Form</a></p>
    <p><a href="#" onclick="get_read_permission();" />Get Read Permission</a></p>
  <?php } ?>
  <a name="fb_share" type="button_count" share_url="http://www.google.com">Share</a>
  <script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
  <script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/en_GB" type="text/javascript"></script>
  <script type="text/javascript">
    FB.init("<?php echo API_KEY; ?>", "xd_receiver.htm", {'reloadIfSessionStateChanged': true});
    FB_RequireFeatures(["Comments"],
      function() {
        FB.CommentClient.add_onComment(function(comment) { alert("UID: " + comment.user + " Added: " + comment.post); });
        FB.CommentClient.remove_onComment();
      }
    );
    function get_permissions() {
      FB_RequireFeatures(["Connect"],
        function() {
          FB.Connect.showPermissionDialog("publish_stream,read_stream", function(response) { alert(response); }, true, null);
        }
      );
    }
    function get_write_permission() {
      FB_RequireFeatures(["Connect"],
        function() {
          FB.Connect.showPermissionDialog("publish_stream", publish_to_stream, false, null);
        }
      );
    }
    function get_read_permission() {
      FB_RequireFeatures(["Connect"],
        function() {
          FB.Connect.showPermissionDialog("read_stream", function(response) { alert(response); }, false, null);
        }
      );
    }
    function publish_to_stream(response) {
      if(response) {
        FB_RequireFeatures(["Connect"],
          function() {
            user_message = 'This is a test.';
            action_links = target_id = actor_id = null;
            attachment = <?php echo $example_2_json; ?>;
            user_message_prompt = 'What\'s on your mind?';
            auto_publish = false;
            FB.Connect.streamPublish(user_message, attachment, action_links, target_id, user_message_prompt, function(post_id, exception, data) { alert(post_id + ', ' + exception + ', ' + data.user_message); } , auto_publish, actor_id);
          }
        );
      } else {
        alert('Extended Permissions Denied');
      }
    }
  </script>
</body>
</html>