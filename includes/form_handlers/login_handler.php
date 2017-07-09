<?php 

    if(isset($_POST['login_button'])){
        $Email = filter_var($_POST['log_Email'], FILTER_SANITIZE_EMAIL); //sanitize Email
        
        $_SESSION['log_Email'] = $Email;    //store email into session variable
        $pswd = md5($_POST['log_pswd']);    //get password
        
        $check_database_query = mysqli_query($con, "Select * from Users where Email = '$Email' and password = '$pswd'");
        $check_login_query = mysqli_num_rows($check_database_query);
        
        if($check_login_query == 1){
            $row = mysqli_fetch_array($check_database_query);   //access results from the MySql query
            $username = $row['username'];
            
            $user_closed_query = mysqli_query($con, "Select * from Users where Email = '$Email' and user_closed = 'yes'");
            
            if(mysqli_num_rows($user_closed_query) == 1){
                $reopen_account = mysqli_query($con, "Update Users set user_closed = 'no' where Email = '$Email'");
            }
            
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        }
        else{
            array_push($error_array, "Email or Password is incorrect.<br>");
        }
        
    }

?>