<html>
    <head>
        <title></title>
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <link href="https://fonts.googleapis.com/css?family=Fresca" rel="stylesheet">
    </head>
    <body>
        
        <style type="text/css">
            body{
                background-color: #fff;
            }
        
            form {
                position: absolute;
                top: 0;
            }
        </style>
        
        <?php
            require 'config/config.php';
            include ('includes/classes/User.php');
            include ('includes/classes/Post.php');
            include ('includes/caches/use_redis.php');
        
	// Select data from the corresponding tables
            if(isset($_SESSION['username'])){
                $userLoggedIn = $_SESSION['username'];
                //Check for user details in Users table
                if($redis->exists("Users_details_".$userLoggedIn)){
                    $user = $redis->get("Users_details_".$userLoggedIn);
                }else{
                    $user_details_query = mysqli_query($con, "Select * from Users where username='$userLoggedIn'");
                    $user = mysqli_fetch_array($user_details_query);
                    $redis->set("Users_details_".$userLoggedIn, $user);
                }
                
            }else{
                header("Location: register.php");
            }

        
        //Get id of post
        if(isset($_GET['post_id'])){
            $post_id = $_GET['post_id'];
        }
        if($redis->exists("Posts_like_".$post_id)){
            $row = $redis->get("Posts_like_".$post_id);
        }else{
            $get_likes = mysqli_query($con, "select likes,added_by from Posts where id='$post_id'") ;
            $row = mysqli_fetch_array($get_likes);
            $redis->set("Posts_like_".$post_id, $row);
        }
        
        
        $total_likes = $row['likes'];
        $user_liked = $row['added_by'];
        //Check for Users details based on user_liked
        if($redis_exists("Users_details_".$user_liked)){
            $row = $redis->get("Users_details_".$user_liked);
        }else{
            $user_details_query = mysqli_query($con, "select * from Users where username = '$user_liked'");
            $row = mysqli_fetch_array($user_details_query);
            $redis->set("Users_details_".$user_liked, $row);
        }
        $total_user_likes = $row['num_likes'];
        
        //like button. Update the respective tables
        if(isset($_POST['like_button'])){
            $total_likes++;
            $query = mysqli_query($con, "update Posts set likes='$total_likes' where id = '$post_id'");
            $total_user_likes++;
            $users_likes = mysqli_query($con, "update Users set num_likes='$total_user_likes' where username = '$user_liked'");
            $insert_user = mysqli_query($con, "insert into likes values('','$userLoggedIn', '$post_id')");
            
            //Insert Notification
            if($user_liked != $userLoggedIn) {
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $user_liked, "like");
            }
        }
        
        //unlike button. Update the respective tables
        if(isset($_POST['unlike_button'])){
            $total_likes--;
            $query = mysqli_query($con, "update Posts set likes='$total_likes' where id = '$post_id'");
            $total_user_likes--;
            $users_likes = mysqli_query($con, "update Users set num_likes='$total_user_likes' where username = '$user_liked'");
            $insert_user = mysqli_query($con, "delete from likes where username = '$userLoggedIn' and post_id='$post_id'");

        }
        //Check for details from likes table based on userLoggedIn and post_id
        if($redis->exists("Likes_details_".$userLoggedIn."_".$post_id)){
            $num_rows = $redis->get("Likes_details_".$userLoggedIn."_".$post_id);
        }else{
            $check_query = mysqli_query($con, "select * from likes where username = '$userLoggedIn' and post_id='$post_id'");
            $num_rows = mysqli_num_rows($check_query);
            $redis->set("Likes_details_".$userLoggedIn."_".$post_id, $num_rows);
        }
        
        
        if($num_rows > 0){
            echo '<form action ="like.php?post_id='.$post_id.'" method = "post">
                    <input type="submit" class="comment_like" name="unlike_button" value="Unlike">
                    <div class = "like_value">
                        likes('.$total_likes.') 
                    </div>
                    
                    
                  </form>
                    ';
        }else{
            echo '<form action ="like.php?post_id='.$post_id.'" method = "post">
                    <input type="submit" class="comment_like" name="like_button" value="Like">
                    <div class = "like_value">
                        likes('.$total_likes.')
                    </div>
                    
                    
                  </form>
                    ';
        }
        
        ?>
        
    </body>


</html>
