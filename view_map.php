<?php
//include files needed for database connection 
require_once 'Db.php';
//create new instance of Db class
$db = new Db();
//if patient's id are being passed to this file
if( isset($_GET["id"])  ){
	//assign user's if to a variable
    $id = $_GET["id"];
	
}

//get data for all patients including GPS
          $data = $db->getPatientsDataForMap();
		  

?>

  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {
        "packages":["map"],
        // Note: you will need to get a mapsApiKey for your project.
        // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
        "mapsApiKey": "AIzaSyB8zEH6QexGENof7HhKf7Ypk3zmdK8JmR0"
    });
      google.charts.setOnLoadCallback(drawChart);
	  
	  //convert patients' data to json
	  var p = <?php echo json_encode($data); ?>;
	      function drawChart() {
		  
		  
		// create an array of arrays to hold data to display patients location on the map as markers and provide a link to view each patient in the information window of the markers
		var x = new Array();
		//assign first array element
		x[0] = new Array('Lat', 'Long', 'Name');
		//loop through patients data and populate array with corresponding information (latitude, longtitude and link to be diplayed in marker's information window)
		for(var i = 0; i < p.length; i++){
		x[i+1] = new Array(parseFloat(p[i].Lat), parseFloat(p[i].Long), "<a href='view_patient.php?id="+p[i].userId+"&uid=<?php echo $id;?>' target='_blank'>"+p[i].first_name+" "+p[i].last_name+"</a>");
	}
	// convert array to data table
		var data = google.visualization.arrayToDataTable(x);
	// display map with markers
        var map = new google.visualization.Map(document.getElementById('map_div'));
        map.draw(data, {
          showTooltip: true,
          showInfoWindow: true
        });
      }

    </script>
  </head>

  <body>
    <div id="map_div" style=" height: 300px"></div>
  </body>
</html>