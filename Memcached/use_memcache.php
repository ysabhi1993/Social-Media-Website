<?php

//Create an object of Memcached
try{
    	$memcache = new Memcached();
}catch(Exception $e){
    	die($e->getMessage());
}
//Create 4 instances 
$MEMCACHED_SERVERS = array('127.0.0.1', '127.0.0.2', '127.0.0.3','127.0.0.4')
foreach($MEMCACHED_SERVERS as $server){
	$memcache->addServer($server, 11211);
	$memcache->setOption(Memcached::OPT_COMPRESSION, false);
}

?>
