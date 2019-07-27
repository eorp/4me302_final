<?php
//include files needed for database connection and displaying rss feed
require_once 'Db.php';
include 'rss_to_html.php';
//create new instance of Db class
$db = new Db();
//if user's id is not being passed to this file
if( !isset($_GET["id"]) ){
	//redirect users back to login page
	header('Location: index.php');
}
else{//otherwise assign user's if to a variable
    $id = $_GET["id"];
}
//variable to hold user's name
$name = "";
//variable to hold user's role (1-patient, 2-physician, 3-researcher)
$role = 0;
//variable to hold a welcome message
$welcome = "";
$therapyId = 0;
$testSessionId = 0;
$userId = $id;

//get user's data
$userData = $db->getUser($id);
//if user's data is not empty
if(!empty($userData)){
    //get user's first name
    $name = $userData['first_name'];
	//get user's role
    $role = $userData['Role_IDrole'];
	//set welcome message to include user's first name
    $welcome = "Wecome ".$name."!";        
}
?>
<!doctype html>
<html class="no-js" lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4ME302 Assignment 3</title>
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/foundation.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">

  </head>
  <body>
      
     <div class="app-dashboard shrink-medium">
		<div class="row expanded app-dashboard-top-nav-bar">
			<div class="columns medium-2">
			  <button data-toggle="app-dashboard-sidebar" class="menu-icon hide-for-medium"></button>
			  <a class="app-dashboard-logo"><?php echo $welcome; ?></a>
			</div>
   
			<div class="columns shrink app-dashboard-top-bar-actions">
			  <a href="logout.php" class="button">Logout</a>
			 
			</div>
		</div>
  
  <div class="app-dashboard-body off-canvas-wrapper">
    <div id="app-dashboard-sidebar" class="app-dashboard-sidebar position-left off-canvas off-canvas-absolute reveal-for-medium" data-off-canvas>
      <div class="app-dashboard-sidebar-title-area">
        <div class="app-dashboard-close-sidebar">
          <h3 class="app-dashboard-sidebar-block-title">Dashboard</h3>
          <!-- Close button -->
          <button id="close-sidebar" data-app-dashboard-toggle-shrink class="app-dashboard-sidebar-close-button show-for-medium" aria-label="Close menu" type="button">
            <span aria-hidden="true"><a href="#"><i class="large fa fa-angle-double-left"></i></a></span>
          </button>
        </div>
        <div class="app-dashboard-open-sidebar">
          <button id="open-sidebar" data-app-dashboard-toggle-shrink class="app-dashboard-open-sidebar-button show-for-medium" aria-label="open menu" type="button">
            <span aria-hidden="true"><a href="#"><i class="large fa fa-angle-double-right"></i></a></span>
          </button>
        </div>
      </div>
      <div class="app-dashboard-sidebar-inner">
        <ul class="menu vertical">
            <?php   
			//depending on user's role only certain type of information will be displayed
			  if($role==1){
				  //for patients - youtube videos
				  echo '<li><a href="#panel1" class="is-active">
					<i class="large fa fa-youtube-square"></i><span class="app-dashboard-sidebar-text">Videos</span>
				  </a></li>';
			  } else if($role==2){//for physicians - information about therapies that this particular physician prescribed
				  echo '<li><a href="#panel1" class="is-active">
					<i class="large fa fa-id-card-o"></i><span class="app-dashboard-sidebar-text">View Patients</span>
				  </a></li>';
			  }else if($role==3){//for researcher - all patients data 
				  echo '<li><a href="#panel1" class="is-active">
					<i class="large fa fa-id-card-o"></i><span class="app-dashboard-sidebar-text">View Patients Details</span>
				  </a></li>';
				  echo '<li><a href="#panel2" class="is-active">
					<i class="large fa fa-id-card-o"></i><span class="app-dashboard-sidebar-text">View Patients on Map</span>
				  </a></li>';
				  //as well as rss feed
				  echo '<li><a href="#panel3" >
					<i class="large fa fa-rss-square"></i><span class="app-dashboard-sidebar-text">RSS</span>
				  </a></li>';
			  }
			  
			?>
          
        </ul>
      </div>
    </div>

    <div class="app-dashboard-body-content off-canvas-content" data-off-canvas-content >
        <div class="app-dashboard-body-content off-canvas-content" data-off-canvas-content id="panel1">
        
      <?php 
      //for patients - include file that handles displaying youtube videos
      if($role==1){
          include 'patient.php';
      }
      else if($role==2){//for physicians
	  //get corresponding data about users ans therapies from database
        $data = $db->getDataForPhysician($id);
        //provided the data array is not empty
       if(!empty($data)){
         //display the results 
			displayData($data);
       }
       else{//if this partucular physician did not prescribe any therpies to any patients
	   //for testing purposes only -provide link with sample data avalable for physicians
           echo 'You do not have any patients assigned to you<br/>
           You can view data for a physician at the following <a href="https://4me302.000webhostapp.com/member_panel.php?id=1">link</a>';
       }
            
      }else if($role==3){//for researches
	  //get data for all patients
          $data = $db->getPatientsData();
		  //provided that data is not empty
          if(!empty($data)){
          //display that data
			displayData($data);
       }
       echo '</div>
	   
	   <div class="app-dashboard-body-content off-canvas-content" data-off-canvas-content id="panel2">';
	   include 'view_map.php';
           //display a map with all patients' locations positioned on it in the form of the markers 
	   echo '</div>
	   
       <div class="app-dashboard-body-content off-canvas-content" data-off-canvas-content id="panel3">';
           //also output RSS feed showing 20 articles, displaying dates and description, with maximum of 200 words showing for each article preview
			output_rss_feed('https://www.news-medical.net/tag/feed/Parkinsons-Disease.aspx', 20, true, true, 200);
	   echo '</div>';
      }
      
//this function takes an array of data as input and presents data in certain tabular format      
function displayData($data){
	//create new instance of database class
    $db = new Db();
	
	
    if(isset($_GET['add'])){
		$success = $_GET['add'];
		if($success == 1)
		{
			echo '<div class="callout success" data-closable="slide-out-right">
  <h5>The notes had been added sucessfully.</h5>
  
  <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
    <span aria-hidden="true">&times;</span>
  </button>
</div>';
		}
		else{
			echo '<div class="callout alert" data-closable="slide-out-right">
  <h5>There had been a problem adding the notes.</h5>
  
  <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
    <span aria-hidden="true">&times;</span>
  </button>
</div>';
		}
	}
           echo '<table>
                  <thead>
                    <tr>
                      <th>Patient\'s<br/> details</th>
                      <!--<th>Patient\' email</th>
                      <th>Organization</th>-->
                      <th>Therapy</th>
                      <th>Medicine</th>
                    </tr>
                  </thead>
                  <tbody>';
                    
       foreach($data as $array){
		 //get therapy id from array to dig deeper
        $therapyId = $array['tid'];
		//get data for test sessions for each therapy
        $session_data = $db->getSessionData($therapyId);
        echo '<tr><td>';
                echo $array['first_name']." ".$array['last_name']."<br/>
				".$array['email']."<br/> Trialed at ".$array['oname']."</td>";
                                echo "<td>";
                if(!empty($session_data)){
                echo '<ul class="accordion" data-accordion data-allow-all-closed="true">
                          <li class="accordion-item " data-accordion-item>
                            <a href="#" class="accordion-title">';
                            echo $array['tname'];
                            echo '</a>
                            <div class="accordion-content" data-tab-content>';
                            echo '<table>
                                      <thead>Session Details
                                        <tr>
                                          <th>ID</th>
                                          <th>Date</th>
                                          <th>Data</th>
							                                        </tr>
                                      </thead>
                                      <tbody>';
                                        
                           foreach($session_data as $value){
							   //get test session id from session data array
                            $testSesionId = $value['sid'];
                            //get data related to notes left for each test session
                            $notes_data = $db->getNotes($testSesionId);
                            echo '<tr><td>';
                                    echo $value['type']."</td><td>";
                                    echo $value['dateTime']."</td><td>";
                                    if(!empty($notes_data)){
                                        echo '<ul class="accordion" data-accordion data-allow-all-closed="true">
                                                  <li class="accordion-item " data-accordion-item>
                                                    <a href="#" class="accordion-title">';
                                                    echo $value['DataURL'];
                                                    echo '</a>
                                                    <div class="accordion-content" data-tab-content>';
                                                    echo '<table>
                                                              <thead>
                                                                <tr>
                                                                  <th>Note</th>
                                                                  <th>Made by</th>
                                                                  <th>Email</th>
                                                                </tr>
                                                              </thead>
                                                              <tbody>';
                                                              foreach($notes_data as $val){
                                                                  echo '<tr><td>';
                                                                  echo $val['note']."</td><td>";
                                                                  echo $val['first_name']." ".$val['last_name']."<br/>(".$val['name'].")</td><td>";
                                                                  echo $val['email']."</td>";
                                                                  
                                                              }
                                                             
                                                              echo "</tbody></table>";
															  //provide option to download patients data
                                                              echo '<a href="'.$value['download'].'" class="button expanded">Download Data</a>';
									   //if patients data can be viewed as html - provide a link to html file
                                       if(!empty($value['view']))
                                       echo '<br/> <a href="'.$value['view'].'" target="_blank" class="button expanded">View Data</a>';
								   //allow physicians and researches to add notes
								   //the form passes notes, id of the user naking the notes and test session id
								   echo "<br/><form action='add_notes.php' method='post'>
											<div class='name-field'>
    
      
	  <textarea placeholder='Type your notes here' required name='notes'></textarea>
	  <input type='hidden' name='sid' value=".$testSesionId.">
	  <input type='hidden' name='uid' value=".$GLOBALS['id'].">
    </label>
    
  </div>
  <button type='submit' class='button expanded'>Add note</button>
											</form>";
                              
                                                echo '</div>
                                              </li>
                                              
                                            </ul>';
                                    }
                                    else{
                                       echo $value['DataURL'].'<br/> <a href="'.$value['download'].'" class="button expanded">Download Data</a>';
                                       if(!empty($value['view']))
                                       echo '<br/> <a href="'.$value['view'].'" target="_blank" class="button expanded">View Data</a>';
								   echo "<br/><form action='add_notes.php' method='post'>
											<div class='name-field'>
    
      
	  <textarea placeholder='Type your notes here' required name='notes'></textarea>
	  <input type='hidden' name='sid' value=".$testSesionId.">
	  <input type='hidden' name='uid' value=".$GLOBALS['id'].">
    </label>
    
  </div>
  <button type='submit' class='button expanded'>Add note</button>
											</form>";
                                       echo "</td>"; 
                                    }
									        
                            }
                                echo "</tbody></table>";
                              
                            echo '</div>
                          </li>
                          
                        </ul>';
                }
                else{
                    echo $array['tname'];
                }
                echo "</td><td>";
                echo $array['mname']."<br/> at ".$array['Dosage']."</td></tr> ";
                
            }
            echo "</tbody></table>";
}
      ?>
</div>
    </div>
  </div>

    <script src="js/vendor/jquery.js"></script>
    <script src="js/vendor/what-input.js"></script>
    <script src="js/vendor/foundation.js"></script>
    <script src="js/app.js"></script>
  </body>
</html>
