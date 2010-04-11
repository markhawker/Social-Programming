 </div>
 <div id="footer"> 
  <p>Themed by <a href="http://markhawker.tumblr.com">markhawker</a> using original theme by <a href="http://www.tumblr.com/themes/by/sparo">sparo</a>.</p> 
 </div>
</div>
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
</body>
</html>