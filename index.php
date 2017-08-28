<?php
    include 'includes/header.php';
    include 'includes/caches/use_redis.php';

    //For Memcached:
    //include("includes/caches/use_memcache.php");
    //replace $redis with $memcached

    if(isset($_POST['post'])){
        $post = new Post($con, $userLoggedIn);
        $post->submitPost($_POST['post_text'],'none');
    }

    
?>

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
        
        <div class="user_details column">

            <h4>Popular</h4>

            <div class="trends">
                <?php 
                //Check for details of Trends table
                if($redis->exists("Trends_index_all_records")){
                    $query = $redis->get("Trends_index_all_records");
                }else{
                    $query_from_table = mysqli_query($con, "SELECT * FROM Trends ORDER BY hits DESC LIMIT 9");
                    $query = mysqli_fetch_array($query_from_table);
                    $redis->set("Trends_index_all_records", $query);
                }

                foreach ($query as $row) {

                    $word = $row['title'];
                    $word_dot = strlen($word) >= 14 ? "..." : "";

                    $trimmed_word = str_split($word, 14);
                    $trimmed_word = $trimmed_word[0];

                    echo "<div style'padding: 1px'>";
                    echo $trimmed_word . $word_dot;
                    echo "<br></div><br>";


                }

                ?>
            </div>


        </div>

        <script>
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
            
            $(document).ready(function(){
                
                $('#loading').show(); 
                
                //Original ajax request for loading first posts
                $.ajax({
                   url:"includes/handlers/ajax_load_posts.php",
                    type: "POST",
                    data: "page=1&userLoggedIn=" + userLoggedIn,
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
