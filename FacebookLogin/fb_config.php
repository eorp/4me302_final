<?php
//check if session has started, if not - start the session
if (version_compare(phpversion(), '5.4.0', '<')) {
     if(session_id() == '') {
        session_start();
     }
 }
 else
 {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
 }

//include the autoloader provided in the SDK
require_once 'Facebook/autoload.php';
//include required libraries
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

//define Facebook app ID and secret ID and callback url
if(!defined('FB_ID')) define( 'FB_ID', '471175146721927');
if(!defined('FB_SECRET')) define( 'FB_SECRET', 'e88baa95d5333b29c04b96d564e09624');
if(!defined('FB_CALLBACK')) define( 'FB_CALLBACK', 'https://4me302.000webhostapp.com/FacebookLogin/');

/*
 * Configuration and setup Facebook SDK
 */
$appId         = FB_ID; //Facebook App ID
$appSecret     = FB_SECRET; //Facebook App Secret
$redirectURL   = FB_CALLBACK; //Callback URL
$fbPermissions = array('email');  //Optional permissions
$fb = new Facebook(array(
    'app_id' => $appId,
    'app_secret' => $appSecret,
    'default_graph_version' => 'v2.10',
));

// Get redirect login helper
$helper = $fb->getRedirectLoginHelper();

// Try to get access token
try {
    if(isset($_SESSION['facebook_access_token'])){
        $accessToken = $_SESSION['facebook_access_token'];
    }else{
        $accessToken = $helper->getAccessToken();
        //$_SESSION['facebook_access_token'] = $accessToken;
    }
} catch(FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

?>