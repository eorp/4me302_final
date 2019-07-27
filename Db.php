<?php
/*
-----------
DB Settings
-----------
*/

if(!defined('DB_USER')) define( 'DB_USER', '4me302' );
if(!defined('DB_NAME')) define( 'DB_NAME', 'pd_db' );
if(!defined('DB_HOST')) define( 'DB_HOST', 'localhost' );
if(!defined('DB_PASS')) define( 'DB_PASS', '4me302pass' );

class Db {
	
	//variable to hold name of the table containing users
	private $userTbl = 'User';
	
	//constructor  - initialising database connection
	function __construct(){
		//if connection does not exist
		if(!isset($this->db)){
            //connect to the database
            $conn = new mysqli(DB_HOST, DB_USER,DB_PASS, DB_NAME);
			//display errors if any
            if($conn->connect_error){
                die("Failed to connect to MySQL: " . $conn->connect_error);
            }else{//connect to database
                $this->db = $conn;
            }
        }
	}
		//this function is used to store or update information about the users logging in to the system. The function takes an array containing user's info and returns user's id from the database after it's been successfully saved or updated
		function storeUserData($userData = array()){
		//variable to hold user's role id (1-patient(default), 2-researcher, 3-physician)
		$role = 1;
		//variable to hold organisation's id (1-hospital for patients & physicians(default), 2-LNU University for researchers)
		$organisation = 1;
		//variable to hold user's id
		$id = 0;
		//if user data array is not empty
		if(!empty($userData)){
			//assign user role and organisation according to oauth_provider
			//patient logins in via Facebook
			if($userData['oauth_provider']=="facebook"){
				$role = 1;
				$organisation = 1;
			}else if($userData['oauth_provider']=="twitter"){//researcher logs in via Twitter
				$role = 2;
				$organisation = 2; 
			}else if($userData['oauth_provider']=="google"){//physician logs in via Google
				$role = 3;
				$organisation = 1;
			}
            //check whether user data exists in database
            $checkQuery = "SELECT * FROM ".$this->userTbl." WHERE oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."'";
            //run the query
            $checkResult = $this->db->query($checkQuery);
            
			//if user record exists in the database
            if($checkResult->num_rows > 0){
                //update user data
                $query = "UPDATE ".$this->userTbl." SET first_name = '".$userData['first_name']."', last_name = '".$userData['last_name']."', email = '".$userData['email']."', Role_IDrole='".$role."', Organization='".$organisation."', modified = NOW(), username = '".$userData['oauth_uid']."' WHERE oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."'";
				//run the update query
                $update = $this->db->query($query);
            }else{//if it is a new user
                //insert user data into the database
                $query = "INSERT INTO ".$this->userTbl." SET oauth_provider = '".$userData['oauth_provider']."', oauth_uid = '".$userData['oauth_uid']."', first_name = '".$userData['first_name']."', last_name = '".$userData['last_name']."', email = '".$userData['email']."', Role_IDrole='".$role."', Organization='".$organisation."', username = '".$userData['oauth_uid']."', created = NOW(), modified = NOW()";
                
				//run the insert query
                $insert = $this->db->query($query);
            }
            
            // Get user data from the database
            $result = $this->db->query($checkQuery);
            
            $userData = $result->fetch_assoc();
			//get user's id
			$id = $userData["userID"];
			
        }
		return $id;
	}

	
	
	function getUserData($userData = array()){
		if(!empty($userData)){
            // Check whether user data already exists in database
            $prevQuery = "SELECT * FROM ".$this->userTbl." WHERE oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."'";
            $prevResult = $this->db->query($prevQuery);
			// Get user data from the database
            $result = $this->db->query($prevQuery);
            $userData = $result->fetch_assoc();
        }
		// Return user data
        return $userData;
	}
	
	//this function takes user's id as a parameter and returns user's data from the database as an array
	function getUser($id){
		//initialise array to hold user's data
		$data = array();
		//run query to find user in the database by id provided
	    $query = "SELECT * FROM ".$this->userTbl." WHERE userID = '".$id."'";
	    $result = $this->db->query($query);
		//store result of the quesry in the array
	    $data = $result->fetch_assoc();
	    
	    return $data;
	}
	
	//this function takes user's (physician's) id as a parameter and returns information as an array about therapies and other relevant inforamation that had been performed by this particular physician
	function getDataForPhysician($id){
	    $data = array();
	    $query = "SELECT tl.name AS tname,tl.Dosage, m.name AS mname, u.email,u.first_name,u.last_name, o.name AS oname, t.therapyID AS tid
                    FROM Therapy AS t 
                    INNER JOIN Therapy_List as tl ON t.TherapyList_IDtherapylist=tl.therapy_listID  
                    INNER JOIN Medicine AS m ON tl.Medicine_IDmedicine = m.medicineID
                    INNER JOIN User AS u ON t.User_IDpatient=u.userID
                    INNER JOIN Organization AS o ON u.Organization=o.OrganizationID
                    WHERE t.User_IDmed = '".$id."'";
                    
        $result = $this->db->query($query);
        while( $row = $result->fetch_assoc()){
            $data[] = $row; 
        }
	    
	    return $data;
	}
	
	//this function returns all relevant information about the patients as an array - to be displayed for the researchers
	function getPatientsData(){
	    $data = array();
		//run the query for all the users who are assigned a patient's role
	    $query = "SELECT tl.name AS tname,tl.Dosage, m.name AS mname, u.email,u.first_name,u.last_name, o.name AS oname, t.therapyID AS tid FROM Therapy AS t INNER JOIN Therapy_List as tl ON t.TherapyList_IDtherapylist=tl.therapy_listID INNER JOIN Medicine AS m ON tl.Medicine_IDmedicine = m.medicineID INNER JOIN User AS u ON t.User_IDpatient=u.userID INNER JOIN Organization AS o ON u.Organization=o.OrganizationID WHERE u.Role_IDrole = 1";
                    
        $result = $this->db->query($query);
        while( $row = $result->fetch_assoc()){
            $data[] = $row; 
        }
	    
	    return $data;
	}
	
	//this function takes therapy id as a parameter and returns an array of data about the test session for that particular therapy
	function getSessionData($id){
	    $data = array();
	    $query = "SELECT ts.type,ts.DataURL, t.dateTime,ts.download_link AS download, ts.view_link AS view, ts.test_SessionID AS sid FROM Test AS t 
                    INNER JOIN Test_Session AS ts ON t.testID=ts.Test_IDtest
                    WHERE t.Therapy_IDtherapy = '".$id."'";
        $result = $this->db->query($query);
        while( $row = $result->fetch_assoc()){
            $data[] = $row; 
        }
	    
	    return $data;
	    
	}
	
	//this function takes test session id as a parameter and returns an array of data concerning notes left regarding this particular test session
	function getNotes($id){
	    $data = array();
	    $query = "SELECT n.note, u.first_name,u.last_name,u.email, r.name FROM Note AS n
                    INNER JOIN Test_Session AS ts ON n.Test_Session_IDtest_session=ts.test_SessionID
                    INNER JOIN User AS u ON n.User_IDmed=u.userID
					INNER JOIN Role AS r ON u.Role_IDrole = r.roleID
                    WHERE ts.test_SessionID = '".$id."'";
                    
        $result = $this->db->query($query);
        while( $row = $result->fetch_assoc()){
            $data[] = $row; 
        }
	    
	    return $data;
	    
	}
	
	//this function returns all relevant information about a particular patient based on id provided as an array - to be displayed for the researchers
	function getPatientData($id){
	    $data = array();
		//run the query for all the users who are assigned a patient's role
	    $query = "SELECT tl.name AS tname,tl.Dosage, m.name AS mname, u.email,u.first_name,u.last_name, o.name AS oname, t.therapyID AS tid FROM Therapy AS t INNER JOIN Therapy_List as tl ON t.TherapyList_IDtherapylist=tl.therapy_listID INNER JOIN Medicine AS m ON tl.Medicine_IDmedicine = m.medicineID INNER JOIN User AS u ON t.User_IDpatient=u.userID INNER JOIN Organization AS o ON u.Organization=o.OrganizationID WHERE u.userID = '".$id."'";
                    
        $result = $this->db->query($query);
        while( $row = $result->fetch_assoc()){
            $data[] = $row; 
        }
	    
	    return $data;
	}
	
	//this function returns full name and id about the patients as an array - to be displayed for the researcherson the map
	function getPatientsDataForMap(){
	    $data = array();
		//run the query for all the users who are assigned a patient's role
	    $query = "SELECT first_name, last_name, userId, Lat, `Long` FROM User WHERE Role_IDrole = 1 AND Lat IS NOT null AND `Long` IS NOT null";
                    
        $result = $this->db->query($query);
        while( $row = $result->fetch_assoc()){
            //$data[] = array('Lat'=>$row['Lat'], 'Lng'=>$row['Long'], 'Name'=> $row['first_name'], 'surname'=> $row['last_name'], 'id'=>$row['userId']);
			$data[] = $row;
        }
	    
	    return $data;
	}
	
	
	
	//function to close database connection
	function closeConn(){
		if(isset($this->db)){
			$this->db->close();
			
		}
	}
	
}
?>