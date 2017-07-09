<?php
    include 'includes/header.php';

    if(isset($_POST['post'])){
        $post = new Post($con, $userLoggedIn);
        $post->submitPost($_POST['post_text'],'none');
    }

    
?>

<html>
    <head>
        <title>Welcome to ConnectMate!</title>
    </head>
    
    <body>
        <div class="user_details column">
            <a href="<?php echo $userLoggedIn; ?>">
                <img src="<?php echo $user['profile_pic']; ?>">
            </a>
            <div class="user_details_left_right">
                <a href="<?php echo $userLoggedIn; ?>">
                    <?php 
                        echo $user['first_name'].' '.$user['last_name'].'<br>';
                    ?>
                </a>

                <?php 
                    echo "Posts: ".$user['num_posts'].'<br>';
                    echo "Likes: ".$user['num_likes']; 
                ?>
            </div>
            
        </div>
        
        <div class="main_column column">
            <form class="post_form" action="index.php" method="post">
                <textarea name="post_text" id="post_text" placeholder="Got Something to say?"></textarea>
                <input type="submit" name="post" id="post_button" value="Post">
                <hr>
            
            </form>
            
            <div class = "post_area"></div>
            
            <img id="loading" src="assets/images/icons/loading.gif">
            
        </div>    
        
        <script>
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
            
            $(document).ready(function(){
                
                $('#loading').show(); 
                
                //Original ajax request for loading first posts
                $.ajax({
                   url:"includes/handlers/ajax_load_posts.php",
                    type: "POST",
                    data: "page=1&userLoggedIn="+userLoggedIn,
                    cache: false,
                    success: function(data){
                        $('#loading').hide();
                        $('.post_area').html(data);
                    }
                });
                
                $(window).scroll(function(){
                    
                    var height = $('.post_area').height();  //div containing posts
                    var scroll_top = $(this).scrollTop();
                    var page = $('.post_area').find('.nextPage').val();
                    var noMorePosts = $('.post_area').find('.noMorePosts').val();
                    
                    if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false'){
                        $('#loading').show();
                        
                        //Original ajax request for loading first posts
                        var ajaxRequest = $.ajax({
                            url:"includes/handlers/ajax_load_posts.php",
                            type: "POST",
                            data: "page=" + page + "&userLoggedIn="+userLoggedIn,
                            cache: false,
                            success: function(response){
                                $('.post_area').find('.next_page').remove(); //remove current .nextpage
                                $('.post_area').find('.noMorePosts').remove(); //remove current .nextpage
                                
                                
                                $('#loading').hide();
                                $('.post_area').append(response);
                            }
                        });
                    }//End if 
                    
                    return false;
                    
                }); //End (window).scroll(function()
                
                
            });
            
        </script>
        
        
        </div>
    </body>


</html>