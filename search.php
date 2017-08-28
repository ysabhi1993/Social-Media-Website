<?php

include("includes/header.php");
include("includes/caches/use_redis.php");
//For Memcached:
//include("includes/caches/use_memcache.php");
//replace $redis with $memcached

if(isset($_GET['q'])) {
	$query = $_GET['q'];
}
else {
	$query = "";
}

if(isset($_GET['type'])) {
	$type = $_GET['type'];
}
else {
	$type = "name";
}
?>

<div class="main_column column" id="main_column">

	<?php 
	if($query == "")
		echo "You must enter something in the search box.";
	else {
		//If query contains an underscore, assume user is searching for usernames
		if($type == "username"){ 
            //Search for the username($query) from Users table in Cache and if necessary in DB
            if($redis->exists("Users_details_".$query)){
                $row = $redis->get("Users_details_".$query);
            }else{
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM Users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
                $row = mysqli_fetch_array($usersReturnedQuery);
                $redis->set("Users_details_".$query, $row);
            }
        }
		//If there are two words, assume they are first and last names respectively
		else {

			$names = explode(" ", $query);
                
			if(count($names) == 3){
                //Check for 3 words searches
                if($redis->exists("User_details_".$names[0]."_".$names[2])){
                    $row = $redis->get("User_details_".$names[0]."_".$names[2]);
                }else{
				    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM Users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed='no'");
                    $row = mysqli_fetch_array($usersReturnedQuery);
                    $redis->set("User_details_".$names[0]."_".$names[2], $row);
                }
            }
			//If query has one word only, search first names or last names 
			else if(count($names) == 2){
                if($redis->exists("User_details_".$names[0]."_".$names[1])){
                    $row = $redis->get("User_details_".$names[0]."_".$names[1]);
                }else{
                    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM Users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no'");
                    $row = mysqli_fetch_array($usersReturnedQuery);
                    $redis->set("User_details_".$names[0]."_".$names[1], $row);
                }
            }
			else {
                if($redis->exists("User_details_first_last_".$names[0])){
                    $row = $redis->get("User_details_first_last_".$names[0]);
                }else{
                    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM Users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no'");
                    $row = mysqli_fetch_array($usersReturnedQuery);
                    $redis->set("User_details_first_last".$names[0], $row);
                }
            }
		}

		//Check if results were found 
		if(count($row) == 0)
			echo "We can't find anyone with a " . $type . " like: " .$query;
		else 
			echo count($row) . " results found: <br> <br>";


		echo "<p id='grey'>Try searching for:</p>";
		echo "<a href='search.php?q=" . $query ."&type=name'>Names</a>, <a href='search.php?q=" . $query ."&type=username'>Usernames</a><br><br><hr id='search_hr'>";

		while($row) {
			$user_obj = new User($con, $user['username']);

			$button = "";
			$mutual_friends = "";

			if($user['username'] != $row['username']) {

				//Generate button depending on friendship status 
				if($user_obj->isFriend($row['username']))
					$button = "<input type='submit' name='" . $row['username'] . "' class='danger' value='Remove Friend'>";
				else if($user_obj->didReceiveRequest($row['username']))
					$button = "<input type='submit' name='" . $row['username'] . "' class='warning' value='Respond to request'>";
				else if($user_obj->didSendRequest($row['username']))
					$button = "<input type='submit' class='default' value='Request Sent'>";
				else 
					$button = "<input type='submit' name='" . $row['username'] . "' class='success' value='Add Friend'>";

				$mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";


				//Button forms
				if(isset($_POST[$row['username']])) {

					if($user_obj->isFriend($row['username'])) {
						$user_obj->removeFriend($row['username']);
						header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
					}
					else if($user_obj->didReceiveRequest($row['username'])) {
						header("Location: requests.php");
					}
					else if($user_obj->didSendRequest($row['username'])) {

					}
					else {
						$user_obj->sendRequest($row['username']);
						header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
					}

				}



			}

			echo "<div class='search_result'>
					<div class='searchPageFriendButtons'>
						<form action='' method='POST'>
							" . $button . "
							<br>
						</form>
					</div>


					<div class='result_profile_pic'>
						<a href='" . $row['username'] ."'><img src='". $row['profile_pic'] ."' style='height: 100px;'></a>
					</div>

						<a href='" . $row['username'] ."'> " . $row['first_name'] . " " . $row['last_name'] . "
						<p id='grey'> " . $row['username'] ."</p>
						</a>
						<br>
						" . $mutual_friends ."<br>

				</div>
				<hr id='search_hr'>";

		} //End while
	}


	?>



</div>
