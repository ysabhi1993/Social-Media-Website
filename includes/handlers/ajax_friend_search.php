<?php 
include("../../config/config.php");
include("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query); 

if(strpos($query, "_") !== false){
    $usersReturned = mysqli_query($con, "select * from Users where username like '$query%' and user_closed='No' limit 8" );
}
else if(count($names) == 2){
    $usersReturned = mysqli_query($con, "select * from Users where (first_name like '%$names[0]%' and last_name like '%$names[1]%') and user_closed='No' limit 8");
}
else{
    $usersReturned = mysqli_query($con, "select * from Users where (first_name like '%$names[0]%' or last_name like '%$names[0]%') and user_closed='No' limit 8");
}


if($query !== ""){
    while($row = mysqli_fetch_array($usersReturned)){
        $user = new User($con, $userLoggedIn);
        
        if($row['username'] != $userLoggedIn){
            $mutual_friends = $user->getMutualFriends($row['username'])." friends in common";
        }
        else{
            $mutual_friends = "";
        }
        
        if($user->isFriend($row['username'])){
            echo "<div class='resultDisplay'>
                <a href='messages.php?u='".$row['username']."'style=color: #000'>
                    <div class='liveSearchProfilePic'>
                        <img src='".$row['profile_pic']."'>
                    </div>
                    <div class='liveSearchText'>".$row['first_name']." ".$row['last_name']."
                        <p style='margin:0'>".$row['username']. "</p>
                        <p id='grey'>".$mutual_friends."</p>
                    </div>
                </a>
            </div>";
        }
    }
}
?>
