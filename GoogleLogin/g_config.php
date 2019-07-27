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


//define Google API client ID and secret ID and callback url
if(!defined('G_ID')) define( 'G_ID', '756440651534-vem1tc7glj5bdk0joodsbo4j8qj6jrg9.apps.googleusercontent.com');
if(!defined('G_SECRET')) define( 'G_SECRET', '7fbam6amiviakle3vW67B7WW');
if(!defined('G_CALLBACK')) define( 'G_CALLBACK', 'https://4me302.000webhostapp.com/GoogleLogin/');

//include required Google API client library files
require_once 'google-api-php-client/Google_Client.php';
require_once 'google-api-php-client/contrib/Google_Oauth2Service.php';


/*
 * Configuration and setup Google API
 */
$clientId = G_ID; 
$clientSecret = G_SECRET; 
$redirectURL = G_CALLBACK; //Callback URL

//call Google API
$gClient = new Google_Client();
$gClient->setApplicationName('Login to 4me302');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);

$google_oauthV2 = new Google_Oauth2Service($gClient);
?>
