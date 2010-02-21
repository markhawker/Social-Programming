<?php

require_once("osapi/external/OAuth.php");

class FriendConnectSignatureMethod extends OAuthSignatureMethod_RSA_SHA1 {
  protected function fetch_public_cert(&$request) {
    return <<<EOD
-----BEGIN CERTIFICATE-----
MIICSjCCAbOgAwIBAgIJAKy5FQe8xeW/MA0GCSqGSIb3DQEBBQUAMCMxITAfBgNV
BAMTGGZyaWVuZGNvbm5lY3QuZ29vZ2xlLmNvbTAeFw0wODEyMTkxOTQ3NDlaFw0w
OTEyMTkxOTQ3NDlaMCMxITAfBgNVBAMTGGZyaWVuZGNvbm5lY3QuZ29vZ2xlLmNv
bTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEAvvHLRIP0tKwFGExqfIOf25Ad
2WHq/Oz4CuuspA75pvXg5+w9E4P5oMNmGNENO7LAA+xSrXLND+FdDg4STH/5FW0Y
ycPhw7LvqsefQwnntn1oHGTYffWRPbovHZBDcCBJZ2cgzXnmsXG9D7rO06fikTaa
6aSw1mVt7sFvwZDegEkCAwEAAaOBhTCBgjAdBgNVHQ4EFgQUITh3OCFLiiyTPEsq
LKmuALCpfXwwUwYDVR0jBEwwSoAUITh3OCFLiiyTPEsqLKmuALCpfXyhJ6QlMCMx
ITAfBgNVBAMTGGZyaWVuZGNvbm5lY3QuZ29vZ2xlLmNvbYIJAKy5FQe8xeW/MAwG
A1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADgYEAvnVl15uganMvEwABVxRwMAys
ICPuzrxvdgAHXDl1/FCv68CEbTatHYPHBTLiY66DL8HICWwFuIx4j0DTuj6IWHRG
iP2BFvtcRvogURixojA+JXsvIjafoShzqAioDptVUx1aIHM+af3B5zE6+wDVmRUz
1WeG3BvhoqPq1pUdWig=
-----END CERTIFICATE-----
EOD;
 }
}
 
$request = OAuthRequest::from_request(null, null, array_merge($_GET, $_POST));
$signature_method = new FriendConnectSignatureMethod();
@$signature_valid = $signature_method->check_signature($request, null, null, $_GET['oauth_signature']);
 
$payload = array();
$payload["validated"] = $signature_valid ? true : false;
$payload["query"] = array_merge($_GET, $_POST);
$payload["rawpost"] = file_get_contents("php://input");

print(json_encode($payload));
 
?>