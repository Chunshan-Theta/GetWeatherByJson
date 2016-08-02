<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$dsn = 'mysql:dbname=maindata;host=192.168.156.236';
$user = 'gavin';
$password = 'gavin';
$conn = new PDO($dsn, $user, $password);
$city = isset($_GET["city"])?$_GET["city"]:null;
$location = isset($_GET["location"])?$_GET["location"]:null;

if($city != null){
	//SELECT * FROM `weatherdata` WHERE `date` >= '2016-07-22' ORDER BY `weather_id` ASC
	$sql = "SELECT * FROM `locationdata` WHERE `city` = '".$city."'";
	$i = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
	$location_id = $i['location_id'];

	//$sql = "SELECT * FROM `weatherdata`  WHERE `location_id` = '".$location_id."';";
	//today date("Y-m-d");"SELECT * FROM `locationdata` WHERE `city` = '".$city."'AND `date` >= '".date("Y-m-d")."' BY `weather_id` ASC"

	$sql="SELECT * FROM `weatherdata` WHERE `location_id` = ".$location_id." AND `date` >= '".date("Y-m-d")."' ORDER BY `weather_id` ASC";
	
	$stack = $conn->query($sql)->fetchAll(PDO::FETCH_OBJ);
	if ($stack==null){
	$sql="SELECT * FROM `weatherdata` WHERE `location_id` = ".$location_id;
	$stack = $conn->query($sql)->fetchAll(PDO::FETCH_OBJ);	
	}
	//$i = $conn->query($sql)->fetchAll(PDO::FETCH_OBJ);
	//echo $sql;


	$stack =json_encode($stack, JSON_FORCE_OBJECT);
	print_r($stack);
	// $stack1 =  json_decode($stack);
	// $t= 4;
	// echo $stack1->$t->time;
	
}else if($location == 0){
	$sql = "SELECT * FROM `locationdata`";
	$stack = $conn->query($sql)->fetchALL(PDO::FETCH_ASSOC);
	
	
	
	

	



	$stack =json_encode($stack, JSON_FORCE_OBJECT);
	print_r($stack);
	
}else if($location != null){
	$sql = "SELECT * FROM `locationdata` WHERE `location_id` = '".$location."'";
	$stack = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);




	$stack =json_encode($stack, JSON_FORCE_OBJECT);
	print_r($stack);
}else{
	echo "no input";	
	
}


?>