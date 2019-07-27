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
//clear session variable
session_unset();
//delete session data from the server
session_destroy();
//redirect user back to login page
header('Location: index.php');
?>