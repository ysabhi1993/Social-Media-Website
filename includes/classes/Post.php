<?php 
class Post{
    private $user_obj;
    private $con;
        
        
    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }
    
    public function submitPost($body, $user_to){
        $body = strip_tags($body);
        $body = mysqli_real_escape_string($this->con, $body);
        $check_empty = preg_replace('/\s+/', '', $body);    //deletes all spaces
        
        if($check_empty != ""){
            
            //current date and time
            $date_added = date("Y-m-d H:i:s");
            //Get Username
            $added_by = $this->user_obj->getUserName();
            
            //if user is on own profile, user_to is 'none'
            if($user_to == $added_by){
                $user_to = "none";
            }
            
            //insert post
            $query = mysqli_query($this->con, "insert into Posts values('','$body','$added_by','$user_to','$date_added','no','no', '0')");
            $returned_id = mysqli_insert_id($this->con);
            
            //insert notification 
            
            
            //update post count for user   
            $num_posts = $this->user_obj->getNumPosts();
            $num_posts++;
            $update_query = mysqli_query($this->con, "update Users set num_posts='$num_posts' where username='$added_by' ");
            
            
        }
    }
    
    public function loadPostFriends($data, $limit){
        
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUserName();
        
        if($page == 1){
            $start = 0;
        }else{
            $start = ($page - 1) * $limit;
        }
        
        $str = "";// string to return
        $data_query = mysqli_query($this->con, "select * from Posts where deleted = 'no' order by id desc");
        
        if(mysqli_num_rows($data_query) > 0){ 
            
            $num_iterations = 0; //number of results checked (not necessarily posted)
            $count = 1;
        
            while($row = mysqli_fetch_array($data_query)){
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];

                //prepare user_to string so it can be included even if not posted by the user
                if($row['user_to'] == 'none'){
                    $user_to = "";
                }else{
                    $user_to_obj = new User($this->con, $row['user_to']);
                    $user_to_name = $user_to_obj->getFirstAndLastName();
                    $user_to = "<a href='".$row['user_to']."'>".$user_to_name."</a>";
                }

                //check if user who posted, has their account closed
                $added_by_obj = new User($this->con, $added_by);
                if($added_by_obj->isClosed()){
                    continue;
                }
                
                    $user_logged_obj = new User($this->con, $userLoggedIn);
                    if($user_logged_obj->isFriend($added_by)){

                        if($num_iterations++ < $start)
                            continue;

                        //Once 10 posts have been loaded, break

                        if($count > $limit)
                            break;
                        else
                            $count++;
                        
                        if($userLoggedIn == $added_by)
                            $delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
                        else
                            $delete_button = "";

                        $user_details_query = mysqli_query($this->con, "select first_name, last_name, profile_pic From Users where username = '$added_by' ");
                        $user_row = mysqli_fetch_array($user_details_query);
                        $first_name = $user_row['first_name'];
                        $last_name = $user_row['last_name'];
                        $profile_pic = $user_row['profile_pic'];
?>

    <script>
        function toggle<?php echo $id; ?>() {
            
            var target = $(event.target);
            if(!target.is('a')){
                var element = document.getElementById("toggleComment<?php echo $id; ?>");

                if(element.style.display == "block")
                    element.style.display = "none";
                else
                    element.style.display == "block";   
            }
        }        
    </script>
<?php
                        $comments_check = mysqli_query($this->con, "select * from Comments where post_id = '$id'");
                        $comment_check_num = mysqli_num_rows($comments_check);
                        
                        //Timeframe

                        $date_time_now = date("Y-m-d H:i:s");
                        $start_date = new DateTime($date_time); //Time of post
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
                        $str .= "<div class = 'status_post' onClick='javascript:toggle$id()'>
                                    <div class = 'post_profile_pic'>
                                        <img src = '$profile_pic' width = 50>
                                    </div>
                                    <div class='posted_by' style='color:#acacac;'>
                                        <a href= '$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                        $delete_button
                                    </div>
                                    <div id='post_body'>
                                        $body
                                        <br>
                                        <br>
                                    </div>
                                    
                                    <div class='newsFeedPostOptions'>
                                        Comments($comment_check_num)&nbsp;&nbsp;&nbsp;&nbsp;
                                        <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                                    </div>
                                    
                                </div>
                                <div class = 'post_comment' id='toggleComment$id' style='display:none;'>
                                    <iframe src='Comment_frame.php?post_id=$id' id='comment_iframe' frame_border='0'></iframe>
                                </div>
                                <hr>"; 
                    }
                
                ?>
            
                <script>
                    $(document).ready(Function(){
                        
                        $('#post<?php echo $id; ?>').on('click', function(){
                            bootbox.confirm("Are you sure you want to delete this post?", function(result){
                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result: result});
                                
                                if(result)
                                    location.reload();
                                
                            });
                        });
                                      
                    });


                </script>

                <?php

                }//End of While loop
            
            if($count > $limit)
                $str.= "<input type='hidden' class='next_page' value='".($page + 1)."'>
                        <input type='hidden' class='noMorePosts' value='false'>";
            else
                $str.= "<input type='hidden' class='noMorePosts' value='true'><p stype='text-align: center;'> No more posts to show </p> ";
        }
        echo $str;
    }
    
     public function loadProfilePosts($data, $limit){
        
        $page = $data['page'];
        $profileUser = $data['profileUsername']; 
        $userLoggedIn = $this->user_obj->getUserName();
        
        if($page == 1){
            $start = 0;
        }else{
            $start = ($page - 1) * $limit;
        }
        
        $str = "";// string to return
        $data_query = mysqli_query($this->con, "select * from Posts where deleted = 'no' and ((added_by='$profileUser' and user_to='none') or user_to='$profileUser') order by id desc");
        
        if(mysqli_num_rows($data_query) > 0){ 
            
            $num_iterations = 0; //number of results checked (not necessarily posted)
            $count = 1;
        
            while($row = mysqli_fetch_array($data_query)){
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];
                
                        if($num_iterations++ < $start)
                            continue;

                        //Once 10 posts have been loaded, break

                        if($count > $limit)
                            break;
                        else
                            $count++;
                        
                        if($userLoggedIn == $added_by)
                            $delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
                        else
                            $delete_button = "";

                        $user_details_query = mysqli_query($this->con, "select first_name, last_name, profile_pic From Users where username = '$added_by' ");
                        $user_row = mysqli_fetch_array($user_details_query);
                        $first_name = $user_row['first_name'];
                        $last_name = $user_row['last_name'];
                        $profile_pic = $user_row['profile_pic'];
?>

    <script>
        function toggle<?php echo $id; ?>() {
            
            var target = $(event.target);
            if(!target.is('a')){
                var element = document.getElementById("toggleComment<?php echo $id; ?>");

                if(element.style.display == "block")
                    element.style.display = "none";
                else
                    element.style.display == "block";   
            }
        }        
    </script>
<?php
                        $comments_check = mysqli_query($this->con, "select * from Comments where post_id = '$id'");
                        $comment_check_num = mysqli_num_rows($comments_check);
                        
                        //Timeframe

                        $date_time_now = date("Y-m-d H:i:s");
                        $start_date = new DateTime($date_time); //Time of post
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
                        $str .= "<div class = 'status_post' onClick='javascript:toggle$id()'>
                                    <div class = 'post_profile_pic'>
                                        <img src = '$profile_pic' width = 50>
                                    </div>
                                    <div class='posted_by' style='color:#acacac;'>
                                        <a href= '$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                        $delete_button
                                    </div>
                                    <div id='post_body'>
                                        $body
                                        <br>
                                        <br>
                                    </div>
                                    
                                    <div class='newsFeedPostOptions'>
                                        Comments($comment_check_num)&nbsp;&nbsp;&nbsp;&nbsp;
                                        <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                                    </div>
                                    
                                </div>
                                <div class = 'post_comment' id='toggleComment$id' style='display:none;'>
                                    <iframe src='Comment_frame.php?post_id=$id' id='comment_iframe' frame_border='0'></iframe>
                                </div>
                                <hr>"; 
                ?>
            
                <script>
                    $(document).ready(Function(){
                        
                        $('#post<?php echo $id; ?>').on('click', function(){
                            bootbox.confirm("Are you sure you want to delete this post?", function(result){
                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result: result});
                                
                                if(result)
                                    location.reload();
                                
                            });
                        });
                                      
                    });


                </script>

                <?php

                }//End of While loop
            
            if($count > $limit)
                $str.= "<input type='hidden' class='next_page' value='".($page + 1)."'>
                        <input type='hidden' class='noMorePosts' value='false'>";
            else
                $str.= "<input type='hidden' class='noMorePosts' value='true'><p stype='text-align: center;'> No more posts to show </p> ";
        }
        echo $str;
    }
    
}



?>
