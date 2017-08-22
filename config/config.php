<?php
    ob_start(); //turns on output buffering
    session_start();
    $timezone = date_default_timezone_set("Europe/London");
        //connecting to the database table using the below connection variable
        $con = mysqli_connect("localhost", "root", "", "Connect_Mate");

        if(mysqli_connect_errno()){
            echo "Failed to connect: ".mysqli_connect_errno();
        }
?>
