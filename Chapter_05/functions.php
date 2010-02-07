<?php

function valid_facebook_session($expires, $session_key, $ss, $user, $valid_signature, $secret) {
 $signature = md5('expires='.$expires.'session_key='.$session_key.'ss='.$ss.'user='.$user.$secret);
 return ($signature == $valid_signature ? true : false);
}

?>