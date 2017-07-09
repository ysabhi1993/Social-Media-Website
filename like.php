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

            if(isset($_SESSION['username'])){
                $userLoggedIn = $_SESSION['username'];
                $user_details_query = mysqli_query($con, "Select * from Users where username='$userLoggedIn'");
                $user = mysqli_fetch_array($user_details_query);
            }else{
                header("Location: register.php");
            }

        
        //Get id of post
        if(isset($_GET['post_id'])){
            $post_id = $_GET['post_id'];
        }
        
        $get_likes = mysqli_query($con, "select likes,added_by from Posts where id='$post_id'") ;
        $row = mysqli_fetch_array($get_likes);
        
        $total_likes = $row['likes'];
        $user_liked = $row['added_by'];
        
        $user_details_query = mysqli_query($con, "select * from Users where username = '$user_liked'");
        $row = mysqli_fetch_array($user_details_query);
        $total_user_likes = $row['num_likes'];
        
        //like button
        if(isset($_POST['like_button'])){
            $total_likes++;
            $query = mysqli_query($con, "update Posts set likes='$total_likes' where id = '$post_id'");
            $total_user_likes++;
            $users_likes = mysqli_query($con, "update Users set num_likes='$total_user_likes' where username = '$user_liked'");
            $insert_user = mysqli_query($con, "insert into likes values('','$userLoggedIn', '$post_id')");
            
            //Insert Notification
        }
        
        //unlike button
        if(isset($_POST['unlike_button'])){
            $total_likes--;
            $query = mysqli_query($con, "update Posts set likes='$total_likes' where id = '$post_id'");
            $total_user_likes--;
            $users_likes = mysqli_query($con, "update Users set num_likes='$total_user_likes' where username = '$user_liked'");
            $insert_user = mysqli_query($con, "delete from likes where username = '$userLoggedIn' and post_id='$post_id'");

        }
        
        
        
        
        
        $check_query = mysqli_query($con, "select * from likes where username = '$userLoggedIn' and post_id='$post_id'");
        $num_rows = mysqli_num_rows($check_query);
        
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