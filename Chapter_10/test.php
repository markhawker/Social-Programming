<?php

$SITE_ID = ''; 
$PARENT_URL = '/';
$FILE_NAME = 'test.php';

if(isset($_REQUEST['authenticate'])) {
  $request = $_REQUEST['authenticate'];
  switch ($request) {
    case 'login':
      header('Location: '.$PARENT_URL.$FILE_NAME.'?loggedin');
      break;
    case 'logout':
      header('Location: '.$PARENT_URL.$FILE_NAME.'?loggedout');
      break;
    default:
      header('Location: '.$PARENT_URL.$FILE_NAME.'');
  }
} else {
  $cookieIdentifier = "fcauth".$SITE_ID;
  $cookie = isset($_COOKIE[$cookieIdentifier]) ? $_COOKIE[$cookieIdentifier] : null;
  $isLoggedIn = $cookie ? true : false;
  $userAgent = $_SERVER['HTTP_USER_AGENT'];
  $unsupportedBrowsers = array(
    'Opera'
  );
  $isBrowserSupported = true;
  foreach ($unsupportedBrowsers as $unsupportedBrowser) {
    $isBrowserSupported = preg_match('/'.$unsupportedBrowser.'/i', $userAgent) ? false : true;
  }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <title>My OpenSocial Test Page</title>
  <!-- Load the Google AJAX API Loader //-->
  <script type="text/javascript" src="http://www.google.com/jsapi"></script>
  <!-- Load the Google Friend Connect JavasSript Library. //-->
  <script type="text/javascript">
    /* <![CDATA[ */
    google.load('friendconnect', '0.8');
    /* ]]> */
  </script>
  <!-- Initialize the Google Friend Connect OpenSocial API. //-->
  <script type="text/javascript">
    /* <![CDATA[ */
    <?php
    if ($isBrowserSupported) { echo 'var isBrowserSupported = true;'; } else { echo 'var isBrowserSupported = false;'; }
    if ($isLoggedIn) { echo ' var isLoggedIn = true;'; } else { echo ' var isLoggedIn = false;'; }
    ?>
    		
    google.friendconnect.container.setParentUrl('<?php echo $PARENT_URL; ?>');
    google.friendconnect.container.initOpenSocialApi({
      site: '<?php echo $SITE_ID; ?>',
      onload: function(securityToken) {
        if (!window.timesloaded) {
          window.timesloaded = 1;
        } else {
          window.timesloaded++;
        }
        if (window.timesloaded > 1) {
          window.location = '<?php echo $PARENT_URL.$FILE_NAME; ?>?authenticate=<?php echo !$isLoggedIn ? 'login' : 'logout'; ?>';
        } else {
          initAllData();
        }
      }
    });	
    /* ]]> */
  </script>
</head>
<body>
<h1>Welcome to OpenSocial Test</h1>
  <div id="viewerControlPanel">
    <p id="gfcButton"></p>
  </div>
  <?php
  
  if($cookie) {
    // Set Up Google Friend Connect Provider 	
    echo '<p>We have the Google Friend Connect cookie.</p>';
    require_once "osapi/osapi.php";
    $provider = new osapiFriendConnectProvider();
    $authentication = new osapiFCAuth($cookie);
    $opensocial = new osapi($provider, $authentication);
	$batch = $opensocial->newBatch();
	$viewerParameters = array(
	  'userId' => '@me',
	  'groupId' => '@self',
      'fields' => '@all'
	);
	$getViewer = $opensocial->people->get($viewerParameters);
	$getActivities = $opensocial->activities->get($viewerParameters);
	$batch->add($getViewer, 'viewer');
	$batch->add($getActivities, 'activities');
	$response = $batch->execute();
	$viewer = $response['viewer'];
	if ($viewer instanceof osapiError) {
	  $code = $viewer->getErrorCode();
	  $message = $viewer->getErrorMessage();
	  // Process OpenSocial API Error
    } else {
      $viewerName = htmlentities($viewer->getName());
	  $viewerThumbnailUrl = htmlentities($viewer->getThumbnailUrl());
	  echo '<p>Hello, '.$viewerName.'.</p>';
	  echo '<p>Profile URL: '.$viewer->profileUrl.'</p>';
	  echo '<p>Thumbnail URL: '.$viewer->thumbnailUrl.'</p>';
	  echo '<h2>Photos</h2>';
	  echo '<ul>';
	  foreach($viewer->photos as $photo) {
	    echo '<li>'.$photo['type'].': '.$photo['value'].'</li>';
	  }
	  echo '</ul>';
	  echo '<h2>URLs</h2>';
	  echo '<ul>';
	  foreach($viewer->urls as $url) {
	    echo '<li>'.($url['type'] ? $url['type'] : 'none').': <a href="'.$url['value'].'">'.(!empty($url['linkText']) ? $url['linkText'] : 'Unknown').'</a></li>';
	  }
	  echo '</ul>';
	}
    $activities = $response['activities'];
	if ($activities instanceof osapiError) {
	  $code = $activities->getErrorCode();
	  $message = $activities->getErrorMessage();
	  // Process OpenSocial API Error
    } else {
      echo '<h2>Activities</h2>';
      echo '<ul>';
      foreach($activities->list as $activity) {
        echo '<li>'.htmlentities($activity->getTitle()).'</li>';
      }
      echo '</ul>';
	}
	$batch = $opensocial->newBatch();
    $memberParameters = array(
      'userId' => '@owner',
      'groupId' => '@friends',
      'fields' => '@all',
      'count' => 3,
      'startIndex' => 1
    );
    $getMembers = $opensocial->people->get($memberParameters);
    $batch->add($getMembers, 'members');
    $response = $batch->execute();
    $members = $response['members'];
    if ($members instanceof osapiError) {
      $code = $members->getErrorCode();
      $message = $members->getErrorMessage();
      echo $message;
      // Process OpenSocial API Error
    } else {
      echo '<h2>Members</h2>';
      echo '<ol>';
      foreach($members->list as $member) {
        echo '<li>'.htmlentities($member->getName()).'</li>';
      }
      echo '</ol>';
      echo '<p>Total Results: '.$members->totalResults.'</p>';
    }
  }
  else {
    echo '<p>We don\'t have the Google Friend Connect cookie.</p>';	
  }
  
  ?>
  
  <script type="text/javascript">
    /* <![CDATA[ */
    function initAllData() {
      onData();
    }
    function onData(){
      // Log In Controls
      var gfcButtonHtml = document.getElementById('gfcButton');
      var viewerInfoHtml = document.getElementById('viewerControlPanel');
      // Test If Browser Supported
      if (isBrowserSupported) {
	if (!isLoggedIn) {
          viewerInfoHtml.innerHTML = '<p id="gfcButton"></p>';
          google.friendconnect.renderSignInButton({
            'id': 'gfcButton',
            'style': 'standard'
          });
          gfcButtonHtml.style.display = 'block';
        }
        else {
          html = '<p>';
          html += '<a href="#" onclick="google.friendconnect.requestSettings();">Settings</a> | ';
          html += '<a href="#" onclick="google.friendconnect.requestInvite();">Invite</a> | ';
          html += '<a href="#" onclick="google.friendconnect.requestSignOut();">Sign Out</a>';
          html += '</p>';
          viewerInfoHtml.innerHTML = html;
        }
      } else {
        gfcButtonHtml.style.display = 'none';
        viewerInfoHtml.innerHTML = '<p>We\'re sorry, but your browser is not supported by Google Friend Connect.</p>';
      }
    }
    /* ]]> */
  </script>
</body>
</html>