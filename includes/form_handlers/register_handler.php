<?php
    //Declaring variables for the register form:
    $fName = "";    //First Name    
    $lName = "";    //Last Name    
    $Email = "";    //Email    
    $Email2 = "";   //Email2    
    $pswd = "";     //Password    
    $pswd2 = "";     //Password2    
    $date = "";     //sign-up date
    $error_array = array(); //Holds error messages

    if(isset($_POST['register_button'])){

        //Registration form values read from POST form 

        //First Name
        $fName = strip_tags($_POST['reg_fName']); //remove unwanted html tags
        $fName = str_replace(' ','', $fName);   //remove spaces
        $fName = ucfirst(strtolower($fName));   //Captalize only first letter(Camelcase)
        $_SESSION['reg_fName'] = $fName; //Storing the first name in session variable

        //Last Name
        $lName = strip_tags($_POST['reg_lName']); //remove unwanted html tags
        $lName = str_replace(' ','', $lName);   //remove spaces
        $lName = ucfirst(strtolower($lName));   //Captalize only first letter(Camelcase)
        $_SESSION['reg_lName'] = $lName; //Storing the first name in session variable

        //Email
        $Email = strip_tags($_POST['reg_Email']); //remove unwanted html tags
        $Email = str_replace(' ','', $Email);   //remove spaces
        $Email = ucfirst(strtolower($Email));   //Captalize only first letter(Camelcase)
        $_SESSION['reg_Email'] = $Email; //Storing the Email in session variable

        //Email2
        $Email2 = strip_tags($_POST['reg_Email2']); //remove unwanted html tags
        $Email2 = str_replace(' ','', $Email2);   //remove spaces
        $Email2 = ucfirst(strtolower($Email2));   //Captalize only first letter(Camelcase)

        //Password
        $pswd = strip_tags($_POST['reg_pswd']); //remove unwanted html tags
        //Password2
        $pswd2 = strip_tags($_POST['reg_pswd2']); //remove unwanted html tags

        $date = date("Y-m-d"); //Current Date 

        //check if Emails match
        if($Email == $Email2){
            //Check if Email is in valid format
            if(filter_var($Email, FILTER_VALIDATE_EMAIL)){
                $Email = filter_var($Email, FILTER_VALIDATE_EMAIL); 

                //Check if the user already exists
                $e_check = mysqli_query($con, "select email from Users where email = '$Email'");

                //count number of rows returned
                $num_rows = mysqli_num_rows($e_check);

                if($num_rows > 0){
                    array_push($error_array,"Email already in use.<br>");
                }


            }else{
                array_push($error_array, "Invalid Format.<br>");
            }   

        }else{
            array_push($error_array, "Emails don't Match.<br>");
        }

        //Check if First Name is of correct length
        if(strlen($fName) > 25 || strlen($fName) < 2){
            array_push($error_array, "Your First Name must be between 2 and 25 characters.<br>");
        }

        //Check if Last Name is of correct length
        if(strlen($lName) > 25 || strlen($lName) < 2){
            array_push($error_array, "Your Last Name must be between 2 and 25 characters.<br>");
        }

        //Check if passwords match
        if($pswd != $pswd2){
            array_push($error_array, "Your passwords do not match.<br>");   
        }else{
            //Check if password has unnecessary characters
            if(preg_match('/[^A-Za-z0-9]/', $pswd)){
                array_push($error_array, "Your password can only contain english characters or numbers.<br>");
            }
        }

        //Check if passwords are of correct length
        if(strlen($pswd) > 30 || strlen($pswd) < 6){
            array_push($error_array, "Your password must be between 6 and 30 characters.<br>");
        } 
        
        //Inserting data into the database
        if(empty($error_array)){
            $pswd = md5($pswd); //encrypts password
        
            //Generate username by concatenating First Name and Last Name
            $username = strtolower($fName.'_'.$lName);
            $check_username_query = mysqli_query($con, "Select username from Users where username = '$username'");

            $i = 0;

            //if username exists add number to it
            while(mysqli_num_rows($check_username_query) != 0){
                $i++;
                $username = $username.'_'.$i;
                $check_username_query = mysqli_query($con, "Select username from Users where username = '$username'");
            }

            //Profile picture assignment
            $profile_pic_array = array("head_alizarin.png", "head_amethyst.png", "head_belize_hole.png", "head_carrot.png", "head_deep_blue.png",
                                       "head_emerald.png", "head_green_sea.png", "head_nephritis.png", "head_pete_river.png", "head_pomegranate.png",
                                       "head_pumpkin.png", "head_red.png", "head_sun_flower.png", "head_turqoise.png", "head_wet_asphalt.png", 
                                       "head_wisteria.png");
            $profile_pic_path = "assets/images/profile_pics/defaults/";
            $rand = rand(0, 15);
            $profile_pic = $profile_pic_path.$profile_pic_array[$rand];

            $query = mysqli_query($con, "insert into Users values('','$fName','$lName','$username','$Email','$pswd','$date','$profile_pic','0',
                                  '0','no',',')");

            array_push($error_array,"<span style='color: #14C800;'>You are all set! Go ahead and login! </span><br>");
       
            //Clearing session variables
            $_SESSION['reg_fName'] = "";
            $_SESSION['reg_lName'] = "";
            $_SESSION['reg_Email'] = "";
        }
        
    }
?>