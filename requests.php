<?php
    include 'includes/header.php';
    include 'includes/caches/use_redis.php';
    //For Memcached:
    //include("includes/caches/use_memcache.php");
    //replace $redis with $memcached
?>

<!-- Shows pending friend requests -->
<div class="main_column column" id="main_column">
    
    <h4>Friend Requests</h4>
    
    <?php
    if($redis->exists("friend_request_details_".$userLoggedIn)){
        $row = $redis->get("friend_request_details_".$userLoggedIn);
    }else{
        $query = mysqli_query($con, "select * from friend_request where user_to = '$userLoggedIn'");
        $row = mysqli_fetch_array($query);
        $redis->set("friend_request_details_".$userLoggedIn, $row);
    }
    $query = mysqli_query($con, "select * from friend_request where user_to = '$userLoggedIn'");
    if(count($row) == 0)
        echo "No Friend Requests at this time!";
    else{
        while($row){
            $user_from = $row['user_from'];
            $user_from_obj = new User($con, $user_from);
            
            echo $user_from_obj->getFirstAndLastName()." sent you a friend request!";
            
            $user_from_friend_array = $user_from_obj->getFriendArray();
            
            if(isset($_POST['accept_request'.$user_from])){
                $add_friend_query = mysqli_query($con, "update Users set friend_array=CONCAT(friend_array, '$user_from,') where username = '$userLoggedIn'");
                $add_friend_query = mysqli_query($con, "update Users set friend_array=CONCAT(friend_array, '$userLoggedIn,') where username = '$userfrom'");
                $delete_query = mysqli_query($con, "delete from friend_request where user_to = '$userLoggedIn' and user_from='$user_from' ");
                echo "You are now Friends!";
                header("Location: requests.php");
                
            }
            
            if(isset($_POST['ignore_request'.$user_from])){
                $delete_query = mysqli_query($con, "delete from friend_request where user_to = '$userLoggedIn' and user_from='$user_from' ");
                echo "Friend Request Ignored!";
                header("Location: requests.php");
            }
            
            ?>
    <!-- accept/ignore Friend requests -->
    <form action="requests.php" method="post">
        <input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" value="Accept">
        <input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" value="Ignore">
    </form>
    
        <?php
        }
    }
        
    ?>
    
</div>
