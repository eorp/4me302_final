<?php
//connect to the database
$link = mysqli_connect("localhost", "4me302", "4me302pass", "pd_db");
//variable to hold patient's id
$pid = 0; 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
//id of the user adding the notes (passed from the form)
$uid = mysqli_real_escape_string($link, $_REQUEST['uid']);
//test session id passed from the form
$sid  = mysqli_real_escape_string($link, $_REQUEST['sid']);
//if patient's id is being passed to this page - assign it to the variable
if(isset($_REQUEST['pid']))
{
$pid = mysqli_real_escape_string($link, $_REQUEST['pid']);
}
//notes passed fromt the form
$note = $_REQUEST['notes'];
//variable to hold status of notes insertion to the database
$msg = 0;


// Attempt insert query execution
$sql = "INSERT INTO note (Test_Session_IDtest_session, User_IDmed, note) VALUES ('$sid', '$uid', '$note')";
//if notes had been inserted successfully - assign success status
if(mysqli_query($link, $sql)){
    
	$msg = 1;
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
 
// Close connection
mysqli_close($link);
//redirect back to a corresponding page with a status of the notes insertion
if($pid == 0)
{
header('Location: member_panel.php?id='.$uid.'&add='.$msg);
}
else{
	header('Location: view_patient.php?id='.$pid.'&uid='.$uid.'&add='.$msg);

}
?>