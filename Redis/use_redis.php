<?php
require "predis/autoload.php";
Predis\Autoloader::register();

//Create an Array or arrays to connect to the 4 instances of Redis. 
$REDIS_SERVERS = array(array("127.0.0.1", 6379), array("127.0.0.2", 6380), array("127.0.0.3", 6381), array("127.0.0.4", 6382));

//Connect to the 
try {
	foreach($REDIS_SERVERS as $server_details){
		$redis = new Predis\Client(array(
			"scheme" => "tcp",
			"host" => $server_details[0],
			"port" => $server_details[1]));
		echo "Successfully connected to Redis_".$server_details[1]."\n";
	}
}
catch (Exception $e) {
    echo "Couldn't connected to Redis";
    echo $e->getMessage();
}

?>
