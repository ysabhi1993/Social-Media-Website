<?php 
class User{
    private $user;
    private $con;
        
        
    public function __construct($con, $user){
        $this->con = $con;
        $user_details_query = mysqli_query($con, "select * from Users where username='$user'");
        $this->user = mysqli_fetch_array($user_details_query);
    }
    
    public function getUserName(){
        return $this->user['username'];
    }
    
    public function getNumPosts(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "select num_posts from Users where username = '$username'");
        $row = mysqli_fetch_array($query);
        return $row['num_posts'];
    }
    
    public function getFirstAndLastName(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "select first_name, last_name from Users where username = '$username' ");
        $row = mysqli_fetch_array($query);
        return $row['first_name'].' '.$row['last_name'];
    }
    
    public function getProfilePic(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "select profile_pic from Users where username = '$username' ");
        $row = mysqli_fetch_array($query);
        return $row['profile_pic'];
    }
    
    public function isClosed(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "select user_closed from Users where username = '$username'");
        $row = mysqli_fetch_array($query);
        
        if($row['user_closed'] == 'yes'){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function isFriend($username_to_check){
        $usernameComma = ",".$username_to_check.",";
        
        if((strstr($this->user['friend_array'],$usernameComma) || ($username_to_check == $this->user['username'])))
            return true;
        else
            return false;
    }
    
    public function didReceiveRequest($user_from){
        $user_to = $this->user['username'];
        $check_request_query = mysqli_query($this->con, "select * From friend_request where user_to='$user_to' and user_from='$user_from'");
        if(mysqli_num_rows($check_request_query) > 0)
            return true;
        else
            return false; 
    }
    
    public function didSendRequest($user_to){
        $user_from = $this->user['username'];
        $check_request_query = mysqli_query($this->con, "select * From friend_request where user_to='$user_to' and user_from='$user_from'");
        if(mysqli_num_rows($check_request_query) > 0)
            return true;
        else
            return false; 
    }
    
    public function removeFriend($user_to_remove){
        $logged_in_user = $this->user['username'];
        
        $query = mysqli_query($this->con, "select friend_array from Users where username = '$user_to_remove'");
        $row = mysqli_fetch_array($query);
        $friend_array_username = $row['friend_array'];
        
        $new_friend_array = str_replace($user_to_remove.",", "", $this->user['friend_array'] );
        $remove_friend = mysqli_query($this->con, "update Users set friend_array = '$new_friend_array' where username='$logged_in_user'");
        
        $new_friend_array = str_replace($logged_in_user.",", "", $row['friend_array'] );
        $remove_friend = mysqli_query($this->con, "update Users set friend_array = '$new_friend_array' where username='$user_to_remove'");
    }
    
    public function sendRequest($user_to){
        $user_from = $this->user['username'];
        $query = mysqli_query($this->con, "insert into friend_request values('','$user_to','$user_from')");
    }
    
    public function getFriendArray(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "select friend_array from Users where username = '$username' ");
        $row = mysqli_fetch_array($query);
        return $row['friend_array'];
    }
    
    public function getMutualFriends($user_to_check){
        $mutual_friends = 0;
        $user_array = $this->user['friend_array']; 
        $user_array_explode = explode(",", $user_array);
        
        $query = mysqli_query($this->con, "Select friend_array from Users where username='$user_to_check' ");
        $row = mysql_fetch_array($query);
        $user_to_check_array = $row['friend_array'];
        $user_to_check_array_explode = explode(",", $user_to_check_array);
        
    }
    
}

?>
