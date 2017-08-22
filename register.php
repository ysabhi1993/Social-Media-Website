<?php
    require 'config/config.php';
    require 'includes/form_handlers/register_handler.php';
    require 'includes/form_handlers/login_handler.php';
?>

<html>
    <!-- Display on the website page -->    
    <head>
        <title> Welcome to ConnectMate!</title>
        <link rel="stylesheet" type="text/css" href="assets/css/register_style.css">
        <link href="https://fonts.googleapis.com/css?family=Fresca" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="assets/js/register.js"></script>
    </head>
    
    <body>
        
        <?php
            
            if(isset($_POST['register_button'])){
                echo'
                
                    <script>
                        $(document).ready(function(){
                            $("#first").hide();
                            $("#second").show();
                        });
                    
                    </script>
                
                ';
            }
        
        ?>
        
        
        
        <div class="wrapper">
            
            <div class="login_box">
                <div class="login_header">
                    <h1>ConnectMate</h1>
                    Register or login below!
                </div>
                <!--Here user tries to login into the website-->
                <div id="first">
                    <form action = "register.php" method = "post">
                        <input type="email" name="log_Email" placeholder="Email Address" value="<?php 
                                                                                            if(isset($_SESSION['reg_Email'])){
                                                                                                echo $_SESSION['reg_Email'];
                                                                                            }
                                                                                            ?>" required>
                        <br>
                        <input type="password" name="log_pswd" placeholder="Password">
                        <br>
                        <input type="submit" name="login_button" value="Login">

                        <?php 
                        if(in_array("Email or Password is incorrect.<br>", $error_array)) 
                            echo "Email or Password is incorrect.<br>" ?>
                        <br>
                        <a href="#" id="signup" class="signup">Not Registered? Sign Up!</a>


                    </form>
                </div>
                <!-- Here user tries to register -->
                <div id="second">
                    <form action="register.php" method="post">
                        //Enter the first name 
                        <input type="text" name="reg_fName" placeholder="First Name" value="<?php 
                                                                                            if(isset($_SESSION['reg_fName'])){
                                                                                                echo $_SESSION['reg_fName'];
                                                                                            }
                                                                                            ?>" required>
                        <br>
                        <?php 
                        if(in_array("Your First Name must be between 2 and 25 characters.<br>", $error_array)) 
                            echo "Your First Name must be between 2 and 25 characters.<br>" ?>
                        
                        <!-- Enter the Last name -->
                        <input type="text" name="reg_lName" placeholder="Last Name" value="<?php 
                                                                                            if(isset($_SESSION['reg_lName'])){
                                                                                                echo $_SESSION['reg_lName'];
                                                                                            }
                                                                                            ?>" required>
                        <br>
                        <?php 
                        if(in_array("Your Last Name must be between 2 and 25 characters.<br>", $error_array)) 
                            echo "Your Last Name must be between 2 and 25 characters.<br>" ?>
                        
                        <!-- Enter the Email -->
                        <input type="email" name="reg_Email" placeholder="Email" value="<?php 
                                                                                            if(isset($_SESSION['reg_Email'])){
                                                                                                echo $_SESSION['reg_Email'];
                                                                                            }
                                                                                            ?>" required>
                        <br>
                        
                        <!-- Enter the Email to confirm -->
                        <input type="email" name="reg_Email2" placeholder="Confirm Email" required>
                        <br>
                        <?php 
                            if(in_array("Email already in use.<br>", $error_array)) 
                                echo "Email already in use.<br>"; 
                            else if(in_array("Invalid Format.<br>", $error_array)) 
                                echo "Invalid Format.<br>";
                            else if(in_array("Emails don't Match.<br>", $error_array)) 
                                echo "Emails don't Match.<br>";
                        ?>
                        
                        <!-- Enter Password -->
                        <input type="password" name="reg_pswd" placeholder="Password" required>
                        <br>
                        
                        <!-- Enter Password to confirm -->
                        <input type="password" name="reg_pswd2" placeholder="Confirm Password" required>
                        <br>
                        
                        <!-- check if passwords match -->
                        <?php 
                            if(in_array("Your passwords do not match.<br>", $error_array)) 
                                echo "Your passwords do not match.<br>"; 
                            else if(in_array("Your password can only contain english characters or numbers.<br>", $error_array)) 
                                echo "Your password can only contain english characters or numbers.<br>";
                            else if(in_array("Your password must be between 6 and 30 characters.<br>", $error_array)) 
                                echo "Your password must be between 6 and 30 characters.<br>"; 
                        ?>
                        
                        <!-- Submit the details -->
                        <input type="submit" name="register_button" value="Register">

                        <?php 
                        if(in_array("<span style='color: #14C800;'>You are all set! Go ahead and login! </span><br>", $error_array)) 
                            echo "<span style='color: #14C800;'>You are all set! Go ahead and login! </span><br>" ?>
                        <br>
                        <a href="#" id="signin" class="signin">Already Registered? Sign In!</a>

                    </form>
                </div>
            </div>
        </div>

    </body>
</html>

