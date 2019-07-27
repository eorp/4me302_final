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
 //include required files 
require_once "tw_login.php";
require "Twitter/autoload.php";
require_once "../Db.php";
use Abraham\TwitterOAuth\TwitterOAuth;

	//if session data for user's id exists
	if(isset($_SESSION['userId']))
		if($_SESSION['userId']!=0){
				//redirect user to member page
				header('Location: https://4me302.000webhostapp.com/member_panel.php?id='.$_SESSION['userId']);
				exit;
		}

if(isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) )
	if($_GET['oauth_token'] || $_GET['oauth_verifier']){	
		$connection = new TwitterOAuth(TW_ID, TW_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$access_token = $connection->oauth('oauth/access_token', array('oauth_verifier' => $_REQUEST['oauth_verifier'], 'oauth_token'=> $_GET['oauth_token']));


		$connection = new TwitterOAuth(TW_ID, TW_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);


		$user_info = $connection->get('account/verify_credentials');
		
		$oauth_token = $access_token['oauth_token'];
		$oauth_token_secret = $access_token['oauth_token_secret'];
		//make up an email from first name
		$madeUpEmail = $user_info->name."@email.com";
		
		//get user information
		$user_id = $user_info->id;
		$user_name = $user_info->name;
		$user_pic = $user_info->profile_image_url_https;
		$text = $user_info->status->text;
		$username = $user_info->screen_name;
		
		
				$_SESSION['name'] = $user_name;
				$_SESSION['dp'] = $user_pic;
				$_SESSION['text'] = $text;
				$_SESSION['username'] = $username;
				$_SESSION['id'] = $user_id;
		
		//initialize Db class
		$db = new Db();
		
		$twUserData = array(
			'oauth_provider'=> 'twitter',
			'oauth_uid'     => $user_info->id,
			'first_name'    => $user_info->name,
			'last_name'     => $user_info->name,
			'email'         => $madeUpEmail
		);
		
		
		
		//insert or update user data to the database
		$userId = $db->storeUserData($twUserData);
	
	
	
			
	//if session data for user's id exists
	if(isset($_SESSION['userId']))
		if($_SESSION['userId']!=0){
				//redirect user to member page
				header('Location: https://4me302.000webhostapp.com/member_panel.php?id='.$_SESSION['userId']);
				exit;
		}
				
	}
?>