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
//include necessary files and libraries to create appropriate login URLs
require_once 'GoogleLogin/g_config.php';
require_once 'FacebookLogin/fb_config.php';
require_once "TwitterLogin/Twitter/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
use FacebookLogin\Facebook\Exceptions\FacebookResponseException;
use FacebookLogin\Facebook\Exceptions\FacebookSDKException;


//render social login buttons
$fbLoginURL = $helper->getLoginUrl($redirectURL, $fbPermissions);
//render facebook login button
$fbButton = '<a href="'.htmlspecialchars($fbLoginURL).'" class="login-box-social-button-facebook shadow">Log in with facebook</a>';
$authUrl = $gClient->createAuthUrl();    
//render google login button
$gButton = '<a href="'.filter_var($authUrl, FILTER_SANITIZE_URL).'" class="login-box-social-button-google shadow">Log in with google</a>';
//render twitter login button
$twButton = '<a href="TwitterLogin/tw_login.php" class="login-box-social-button-twitter shadow">Log in with Twitter</a>';

?>
<!doctype html>
<html class="no-js" lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4ME302 Assignment 2</title>
    <link rel="stylesheet" href="css/foundation.css">
    <link rel="stylesheet" href="css/app.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">

  </head>
  <body>
    <div class="grid-container">
	<div class="login-box">
  <div class="row collapse expanded">

    <div class="small-12 medium-6 column small-order-2 medium-order-1">
		<form>
		  <div class="login-box-form-section form-icons">
			<h1 class="login-box-title">Sign in</h1>			
			<div class="input-group">
			  <span class="input-group-label">
				<i class="fa fa-envelope"></i>
			  </span>
			  <input class="input-group-field" type="email" name="email" placeholder="Email">
			</div>
			<div class="input-group">
			  <span class="input-group-label">
				<i class="fa fa-key"></i>
			  </span>
			  <input class="input-group-field" type="password" name="password" placeholder="Password">
			</div>

				
			<input class="login-box-submit-button shadow" type="submit" name="signup_submit" value="Sign me up" />
		</form>
		<p class="form-registration-member-signin">Not a member yet? <a href="#">Sign up</a></p>
      </div>
      <div class="or shadow">OR</div>
    </div>
    <div class="small-12 medium-6 column small-order-1 medium-order-2 login-box-social-section">
      <div class="login-box-social-section-inner">
        <span class="login-box-social-headline shadow">Sign in with<br />your social network</span>
		<?php 
		echo $fbButton;
		echo $twButton;
		echo $gButton;
		?>	
      </div>
    </div>
  </div>
</div>

    </div>

    <script src="js/vendor/jquery.js"></script>
    <script src="js/vendor/what-input.js"></script>
    <script src="js/vendor/foundation.js"></script>
    <script src="js/app.js"></script>
  </body>
</html>
