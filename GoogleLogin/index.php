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

//include config file and Db class
require_once 'g_config.php';
require_once '../Db.php';

//if session data for user's id exists
if(isset($_SESSION['userId']))
if($_SESSION['userId']!=0){
        //redirect user to member page
        header('Location: https://4me302.000webhostapp.com/member_panel.php?id='.$_SESSION['userId']);
        exit;
    }
    //redirect the user back to the same page if url has "code" parameter in query string
	if(isset($_GET['code'])){
		$gClient->authenticate($_GET['code']);
		$_SESSION['token'] = $gClient->getAccessToken();
		header('Location: ./');
	}

if(isset($_SESSION['token'])){
    $gClient->setAccessToken($_SESSION['token']);
}

if($gClient->getAccessToken()){
    //get user profile data from google
    $gpUserProfile = $google_oauthV2->userinfo->get();
    $_SESSION['provider'] = "google";
    
    //initialize database class
    $db = new Db();
    
	//getting user profile info
    $gpUserData = array();
    $gpUserData['oauth_uid']  = !empty($gpUserProfile['id'])?$gpUserProfile['id']:'';
    $gpUserData['first_name'] = !empty($gpUserProfile['given_name'])?$gpUserProfile['given_name']:'';
    $gpUserData['last_name']  = !empty($gpUserProfile['family_name'])?$gpUserProfile['family_name']:'';
    $gpUserData['email']      = !empty($gpUserProfile['email'])?$gpUserProfile['email']:'';
    
    //insert or update user data to the database
    $gpUserData['oauth_provider'] = 'google';
    $userId = $db->storeUserData($gpUserData);
    
    //save user ID in the session
    $_SESSION['userId'] = $userId;
 
//if session data for user's id exists
if(isset($_SESSION['userId']))
if($_SESSION['userId']!=0){
        //redirect user to member page
        header('Location: https://4me302.000webhostapp.com/member_panel.php?id='.$_SESSION['userId']);
        exit;
    }
}
?>