<?php

include '../config.php';
include '../functions.php';
include '../facebook-platform/php/facebook.php';

$facebook = new Facebook(API_KEY, SECRET, $_POST['fb_sig_profile_session_key']);

$facebook->require_frame();

$facebook_parameters = $facebook->get_valid_fb_params($_POST, null, 'fb_sig');
$profile_user = $facebook_parameters['profile_user'];

if($facebook_parameters['in_profile_tab'] == 1) {

?>

<fb:fbml>
 <h1>Tab Page - Test Tube</h1>
 <div id="comment_response"><p>You can submit a test comment by using the form below. On submitting the form you will be prompted to grant permission to write to your stream which will enable your comment to be submitted.</p>
<p><a href="#" onclick="Animation(document.getElementById('comment_response')).to('height', '0px').to('width', '0px').to('opacity', 0).blind().hide().go(); return false;">Hide this Message</a>.</p>
 </div>
 <form>
  <p><label for="comment_text">Comment Text: </label><input type="text" name="comment_text" id="comment_text" value="" size="50" maxlength="140" /></p>
  <p><label for="publish_comment">Publish To Stream: </label><input type="checkbox" name="publish_comment" id="publish_comment" /></p>
  <p><input type="submit" value="Publish Comment" onsubmit="return false;" onclick="submit_form('comment_response'); return false;" /></p>
 </form>
 <h2>Comments</h2>
 <div id="comments_box">
  <fb:comments xid="c_<?php echo $profile_user; ?>" canpost="true" candelete="true"></fb:comments>
 </div>
 <script type="text/javascript">
 <!--
 function submit_form(form) {
  comment_text = document.getElementById('comment_text').getValue();
  if(!comment_text == "") { 
   publish_comment = document.getElementById('publish_comment').getChecked();
   if(publish_comment) {
    Facebook.showPermissionDialog(
     'publish_stream',
     function(response) { 
      if(response) { do_ajax(form, publish_comment); } 
      else { do_ajax(form, false); document.getElementById('publish_comment').setChecked(false); } 
     }, 
     false
    );
   } else {
    do_ajax(form, false);
   }
  } else {
   document.getElementById('comment_text').setStyle({color: 'white', background: 'red'});
  }
 }
function do_ajax(div, publish_comment) {
  comment_text = document.getElementById('comment_text').getValue();
  if(!comment_text == "") { 
   var ajax = new Ajax();
   ajax.responseType = Ajax.JSON;
   ajax.ondone = function(data) {
    document.getElementById(div).setInnerFBML(data.fbml_response);
    reveal(div);
    document.getElementById('comments_box').setInnerFBML(data.fbml_comments);
    document.getElementById('comment_text').setValue('');
    document.getElementById('comment_text').setStyle({color: 'black', background: 'white'});
   }
   ajax.onerror = function() {
    document.getElementById(div).setInnerFBML('<fb:error message="There was an error submitting the form." />');
    reveal(div);
   }
   var params = {
    'comment_text': comment_text,
    'owner': <?php echo $profile_user; ?>,
    'publish_comment': publish_comment
   };
   ajax.requireLogin = 1;
   ajax.post('<?php echo CANVAS_CALLBACK_URL; ?>/post.php', params);
  }
 }
 function reveal(div) {
  Animation(document.getElementById(div)).to('height', 'auto').from(0).to('width', 'auto').from(0).to('opacity', 1).from(0).blind().show().go(); 
  return false;
 }
 //-->
 </script>
</fb:fbml>

<?php

} else {
 $facebook->redirect(CANVAS_PAGE_URL);
}

?>