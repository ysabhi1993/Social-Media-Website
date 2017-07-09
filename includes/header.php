<?php
    require 'config/config.php';
    include 'includes/classes/User.php';
    include 'includes/classes/Post.php';


    if(isset($_SESSION['username'])){
        $userLoggedIn = $_SESSION['username'];
        $user_details_query = mysqli_query($con, "Select * from Users where username='$userLoggedIn'");
        $user = mysqli_fetch_array($user_details_query);
    }else{
        header("Location: register.php");
    }

?>

<html>
    <head>
        <title>Welcome to ConnectMate!</title>
        <!--Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Fresca" rel="stylesheet">
        
        <!--javascript -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/bootbox.min.js"></script>
        <script src="assets/js/connmate.js"></script>
        
        <!-- CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    </head>
    
    <body>
        <div class="top_bar">
            <div class="logo">   
                <a id="ConnectMate" href="index.php">ConnectMate</a>
            </div>
            <nav>
                <a class="first_name" href="<?php echo $userLoggedIn; ?>"> <?php echo $user['first_name']; ?></a>
                <a href="#"><i class="fa fa-home fa-lg"></i></a>
                <a href="#"><i class="fa fa-envelope fa-lg"></i></a>
                <a href="requests.php"><i class="fa fa-users fa-lg"></i></a>
                <a href="#"><i class="fa fa-bars fa-lg"></i></a>
                <a href="includes/handlers/logout.php"><i class="fa fa-sign-out fa-lg"></i></a>
            </nav>
                
        </div>
        
        <div class="wrapper">
            
            
             
    