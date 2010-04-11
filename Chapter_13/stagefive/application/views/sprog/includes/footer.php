 </div>
 <div id="footer"> 
  <p>Themed by <a href="http://markhawker.tumblr.com">markhawker</a> using original theme by <a href="http://www.tumblr.com/themes/by/sparo">sparo</a>.</p> 
 </div>
</div>
<div id="google_social_bar"></div>
<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
<script type="text/javascript">
 FB.init("<?php echo API_KEY; ?>", "<?php echo XD_RECEIVER; ?>", {"reloadIfSessionStateChanged":true});
 function get_permissions() {
   FB_RequireFeatures(["Connect"],
     function() {
       FB.Connect.showPermissionDialog("publish_stream,read_stream,offline_access", parse_permissions, false, null);
     }
   );
 }
 function parse_permissions(response) {
 	var permissions = new Array();
 	permissions = response.split(",");
 	if(permissions.length == 3) {
 	  document.getElementById('facebook_permissions').style.display = "none";
 	  window.location.reload();
 	}
 }
</script>
<script type="text/javascript">
  var viewer;
  function initAllData() {
    var params = {
      'profileDetail': [
        opensocial.Person.Field.ID,
        opensocial.Person.Field.NAME,
        opensocial.Person.Field.THUMBNAIL_URL,
        opensocial.Person.Field.PROFILE_URL
      ]
    };
    var req = opensocial.newDataRequest();
    req.add(req.newFetchPersonRequest('VIEWER', params), 'viewer');
    req.send(onData);
  }
  function onData(data) {
    var gfcButtonHtml = document.getElementById('gfcButton');
    if (data.get('viewer').hadError()) {
      google.friendconnect.renderSignInButton({
        'id': 'gfcButton',
        'style': 'standard'
      });
      gfcButtonHtml.style.display = 'block';
    } else {
      gfcButtonHtml.style.display = 'none';
      window.location.reload();
    }
  }
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
  skin['POSITION'] = 'bottom';
  google.friendconnect.container.renderSocialBar({ 
    id: 'google_social_bar',
    site: '<?php echo GFC_SITE_ID; ?>',
    'view-params':{"showWall":"false"}
  }, skin);
</script>
</body>
</html>