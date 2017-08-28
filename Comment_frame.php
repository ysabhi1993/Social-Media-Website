<html>
    <head>
        <title></title>
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <link href="https://fonts.googleapis.com/css?family=Fresca" rel="stylesheet">
    </head>
    <body>
        
        <style type="text/css">
            *{
                font-size: 12px;
                font-family: 'Fresca', sans-serif;
            }
            
        </style>

        <?php
            require 'config/config.php';
            include ('includes/classes/User.php');
            include ('includes/classes/Post.php');
            include("includes/classes/Notification.php");
            include("includes/caches/use_redis.php");
            
            //For Memcached:
            //include("includes/caches/use_memcache.php");
            //replace $redis with $memcached
            

            if(isset($_SESSION['username'])){
                $userLoggedIn = $_SESSION['username'];
                //Check if the Users details are present in redis
                if($redis->exists("Users_details".$userLoggedIn)){
                    $user = $redis->get("Users_details_".$userLoggedIn);
                }else{
                    $user_details_query = mysqli_query($con, "Select * from Users where username='$userLoggedIn'");
                    $user = mysqli_fetch_array($user_details_query);
                    $redis->set("Users_details_".$userLoggedIn, $user);
                }
            }else{
                header("Location: register.php");
            }

        ?>
        
        <script>
            function toggle(){
                var element = document.getElementById("comment_section");
                
                if(element.style.display == "block")
                    element.style.display = "none";
                else
                    element.style.display == "block";
            }
        
        </script>
        
        <?php 
        
            //Get id of post
            if(isset($_GET['post_id'])){
                $post_id = $_GET['post_id'];
            }
            //Check for added by and user to from the Posts table
            if($redis->exists("Posts_Comment_Frame_".$post_id)){
                $row = $redis->get("Posts_Comment_Frame_".$post_id)
            }else{
                $user_query = mysqli_query($con, "select added_by, user_to from Posts where id='$post_id'");
                $row = mysqli_fetch_array($user_query);
                $redis->set("Posts_Comment_Frame_".$post_id, $row);
            }
            
        
            $posted_to = $row['added_by'];
        
            if(isset($_POST['postComment'.$post_id])){
                $post_body = $_POST['post_body'];
                $post_body = mysqli_escape_string($con, $post_body);
                $date_time_now = date("Y-m-d H:i:s");
                $insert_post = mysqli_query($con, "insert into Comments values('','$post_body','$userLoggedIn','$posted_to','$date_time_now','no','$post_id')");
                
                if($posted_to != $userLoggedIn) {
                    $notification = new Notification($con, $userLoggedIn);
                    $notification->insertNotification($post_id, $posted_to, "comment");
                }

                if($user_to != 'none' && $user_to != $userLoggedIn) {
                    $notification = new Notification($con, $userLoggedIn);
                    $notification->insertNotification($post_id, $user_to, "profile_comment");
                }

                //Check for the details from Comments table
                if($redis->exists("Comments_details_".$post_id)){
                    $row = $redis->get("Comments_details_".$post_id)
                }else{
                    $get_commenters = mysqli_query($con, "SELECT * FROM Comments WHERE post_id='$post_id' order by id asc");
                    $row = mysqli_fetch_array($get_commenters);
                    $redis->set("Comments_details_".$post_id, $row);
                }
                $notified_users = array();
                while($row) {

                    if($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to 
                        && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)) {

                        $notification = new Notification($con, $userLoggedIn);
                        $notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

                        array_push($notified_users, $row['posted_by']);
                    }

                }
                echo "<p>Comment Posted!</p>";
            }
        
        ?>
        
        <form action="Comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="post">
        
            <textarea name="post_body"></textarea>
            <input type ="submit" name="postComment<?php echo $post_id; ?>" value="Post Comment">
        
        </form>
    
        <!-- Load Comments-->
        
        <?php 
            //Check for the details from Comments table
                if($redis->exists("Comments_details_".$post_id)){
                    $comment = $redis->get("Comments_details_".$post_id)
                }else{
                    $get_comments = mysqli_query($con, "select * from Comments where post_id = '$post_id' order by id asc");
                    $comment = mysqli_fetch_array($get_comments);
                    $redis->set("Comments_details_".$post_id, $row);
                }
            
            $count = count($row);
            
            if($count != 0){
                while($comment){
                    
                    $comment_body = $comment['post_body'];
                    $posted_to = $comment['posted_to'];
                    $posted_by = $comment['posted_by'];
                    $date_added = $comment['date_added'];
                    $removed = $comment['removed'];
                                    
                    //Timeframe
                    
                    $date_time_now = date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_added); //Time of post
                    $end_date = new DateTime($date_time_now);//Current Time
                    $interval = $start_date->diff($end_date);//Difference between two dates
                    if($interval->y >= 1){
                        if($interval == 1){
                            $time_message = $interval->y." year ago"; //1 year ago
                        }else{
                            $time_message = $interval->y." years ago"; //1+ year ago
                        }
                    }else if($interval->m >= 1){
                        if($interval->d == 0){
                            $days = " ago"; 
                        }else if ($interval->d == 1){
                            $days = $interval->d. " day ago"; 
                        }else {
                            $days = $interval->d. " days ago"; 
                        }

                        if($interval->m == 1){
                            $time_message = $interval->m." month".$days;
                        }else{
                            $time_message = $interval->m." months".$days;
                        }

                    }else if($interval->d >= 1){
                        if ($interval->d == 1){
                            $time_message = "Yesterday"; 
                        }else {
                            $time_message = $interval->d. " days ago"; 
                        }
                    }else if($interval->h >= 1){
                        if ($interval->h == 1){
                            $time_message = $interval->h. " hour ago"; 
                        }else {
                            $time_message = $interval->h. " hours ago"; 
                        }
                    }else if($interval->i >= 1){
                        if ($interval->i == 1){
                            $time_message = $interval->i. " minute ago"; 
                        }else {
                            $time_message = $interval->i. " minutes ago"; 
                        }
                    }else {
                        if ($interval->s < 30){
                            $time_message = "Just now"; 
                        }else {
                            $time_message = $interval->s. " seconds ago"; 
                        }
                    }
                    
                    $user_obj = new User($con, $posted_by);
        ?>
                    <div class= "comment_section">
                        <a href="<?php echo $posted_by;?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic(); ?>" title="<?php echo $posted_by; ?>" style="float:left; height:30; "></a>
                        <a href="<?php echo $posted_by;?>" target="_parent"><b><?php echo $user_obj->getFirstAndLastName(); ?></b></a>
                        &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message."<br>".$comment_body; ?>
                        <hr>
                    </div>
        <?php
                    
                }
            }
        
        else{
            echo "<center><br> No Comments to show!</center>";
        }
        
        ?>
        
        
        
    </body>


</html>
