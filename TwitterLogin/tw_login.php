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
//include required library files
require "Twitter/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

/*
-----------
Twitter API Settings
-----------
*/

if(!defined('TW_ID')) define( 'TW_ID', 'HqoBn4gfq9Rk5F8qVnp66eJzR');
if(!defined('TW_SECRET')) define( 'TW_SECRET', 'TDSoRS9IsemxswFUB8RXhpj6RzNZ3mdswMXBH5fSJpC9Sl2jiq');
if(!defined('TW_CALLBACK')) define( 'TW_CALLBACK', 'https://4me302.000webhostapp.com');

//create connection with Twitter oauth
$connection = new TwitterOAuth(TW_ID, TW_SECRET);
$request_token = $connection->oauth("oauth/request_token", array("oauth_callback" => "https://4me302.000webhostapp.com/TwitterLogin"));

$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

$url = $connection->url("oauth/authorize", array("oauth_token" => $request_token['oauth_token']));
header('Location: ' . $url);
?>