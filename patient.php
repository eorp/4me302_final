<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
	 <!--[if lt IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    
    
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
 <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Page Content -->
    <div class="container">
 <script src="js/jquery.js"></script>

 <style>
 /* Source: http://bootsnipp.com/snippets/featured/responsive-youtube-player */
 .vid {
    position: relative;
    padding-bottom: 56.25%;
    padding-top: 30px; height: 0; overflow: hidden;
}
 
.vid iframe,
.vid object,
.vid embed {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
 </style>
 
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
       
            <div class="col-lg-12 text-center">
              
			  
                <div id="youtube-gallery"></div>
				
				<script>
				//Load video list
				$("#youtube-gallery").load('youtube_video.php');
				</script>
				
				
            </div>
			
			
			
       
        <!-- /.row -->

		<!-- Footer -->
 		
    </div>
    <!-- /.container -->


   

</body>

</html>