
<?php
//$i=8;
$arr = array("Taiwan", "Xianeibu", "Taipei", "Tainan","EndPoint");
//$arr = array("Japan","Tokyo","Thonje","Fukuoka-shi","EndPoint");
//$arr = array("Kyoto","Suzaka","EndPoint");



echo "<br><br><br>wait...";
$city = isset($_GET["city"])?$_GET["city"]:"EndPoint";
if ($city!="EndPoint"){
	$json = file_get_contents("http://api.openweathermap.org/data/2.5/forecast?q=".$city."&appid=a8f1a35148b9d49f4bd8d2fa2a9d5764");
	echo "http://api.openweathermap.org/data/2.5/forecast?q=".$city."&appid=a8f1a35148b9d49f4bd8d2fa2a9d5764<br>";
	$obj = json_decode($json);

	$city = $obj->city->name;
	for($i = 0;$i<25;$i++)
	{
		$longitude = floor($obj->city->coord->lon);
		$latitude = floor($obj->city->coord->lat);
		$country =$obj->city->country;
		$time = substr($obj->list[$i]->dt_txt,11,8);
		$date =substr($obj->list[$i]->dt_txt,0,10);

	//開氏溫標	[K] = [°C] + 273.15	[°C] = [K] − 273.15
	$maxTemperature = $obj->list[$i]->main->temp_max;
	$maxTemperature = (int)$maxTemperature-273;

	$minTemperature = $obj->list[$i]->main->temp_min;
	$minTemperature = (int)$minTemperature-273; 
	$pressure = $obj->list[$i]->main->pressure;
	$humidity =$obj->list[$i]->main->humidity;
	$chanceOfClouds =$obj->list[$i]->clouds->all;

	if(property_exists($obj->list[$i],"rain") and property_exists($obj->list[$i]->rain, '3h')){
		$t = '3h';
		$volumeOfRain = (float)$obj->list[$i]->rain->$t;
	}
	else{
		$volumeOfRain = 0;
		
	}

	$description =$obj->list[$i]->weather[0]->description;



	echo $i."\n\n";
	echo $city."\n" ; 
	echo $date."\n" ; 
	echo $time."\n"; 
	echo "<br>";
	checkdatenonexist($latitude,$longitude,$city,$country,$time,$date,$maxTemperature,$minTemperature,$pressure,$humidity,$volumeOfRain,$chanceOfClouds,$description);

	}
	
	
	//header('Location: http://192.168.156.236/weatherdata/AutoCatchData.php?city='.$arr[array_search($city, $arr)+1]);
	//header('Location: http://google.com.tw');


}
else{
	echo "end";
	
}


/* INSERT INTO `locationdata` (`location_id`, `latitude`, `longitude`, `city`, `country`) VALUES (NULL, '25', '121', 'Taipei', 'Taiwan'); */
/* INSERT INTO `weatherdata` (`weather_id`, `location_id`, `maxTemperature`, `minTemperature`, `pressure`, `humidity`, `volumeOfRain`, `chanceOfClouds`, `time`, `date`, `description`) VALUES (NULL, '1', '27', '27', '1000', '1000', '0', '0', '09:00:00', '2016-07-15', 'clear sky'); */






function checkdatenonexist($latitude,$longitude,$city,$country,$time,$date,$maxTemperature,$minTemperature,$pressure,$humidity,$volumeOfRain,$chanceOfClouds,$description) {

	echo "checkdatenonexist<br>";
	//該城鎮是否存在於資料庫
	//SELECT * FROM `locationdata` WHERE `city` = 'taipei'
	$pass = mysqlselect("SELECT * FROM `locationdata` WHERE `city` = '".$city."'");
	if($pass==null & $longitude!=0){
		//該城鎮不存在於資料庫
		mysqlQuery("INSERT INTO `locationdata` (`location_id`, `latitude`, `longitude`, `city`, `country`) VALUES (NULL, '".$latitude."', '".$longitude."', '".$city."', '".$country."');");
	} 
	
	$pass = mysqlselect("SELECT * FROM `locationdata` WHERE `city` = '".$city."'");
	$location_id = $pass['location_id'];
	
	
	//該時間是否有記錄過
	//查詢最後紀錄之預測時間
	//SELECT * FROM `weatherdata` WHERE `weatherdata`.`time`>'00:00:00' & `weatherdata`.`location_id`='1' ORDER BY `weatherdata`.`time` ASC;
	
	//最後紀錄時間
	$pass = mysqlselect("SELECT * FROM `weatherdata` WHERE `location_id`='".$location_id."' ORDER BY `date` ASC, `time` ASC;");
	echo $pass['date']."\n";
	echo $pass['time']."<br>";
	
	if($time > $pass['time'] & $date == $pass['date']  || $date > $pass['date'] || $pass == null){
		// 時間晚於最後紀錄時間
		//INSERT INTO `weatherdata` (`weather_id`, `location_id`, `maxTemperature`, `minTemperature`, `pressure`, `humidity`, `volumeOfRain`, `chanceOfClouds`, `time`, `date`, `description`) VALUES (NULL, '1', '27', '27', '1000', '1000', '0', '0', '03:00:00', '2016-07-15', 'rain');
		mysqlQuery("INSERT INTO `weatherdata` 
					(`weather_id`, `location_id`, `maxTemperature`, `minTemperature`, `pressure`, `humidity`, `volumeOfRain`, `chanceOfClouds`, `time`, `date`, `description`) 
					VALUES (NULL, '".$location_id."', '".$maxTemperature."', '".$minTemperature."', '".$pressure."', '".$humidity."', '".$volumeOfRain."', '".$chanceOfClouds."',
					'".$time."', '".$date."', '".$description."');");

		echo "checkdatenonexist<br>";
	}
}




function mysqlQuery($sql) {

	/* Connect to a MySQL database using driver invocation */
	$dsn = 'mysql:dbname=maindata;host=192.168.156.236';
	$user = 'gavin';
	$password = 'gavin';
	
	try {
		$dbh = new PDO($dsn, $user, $password);
		$dbh->query($sql);	
	} catch (PDOException $e) {
		echo 'Connection failed: '.$e->getMessage();
	}
}


function mysqlselect($sql) {

	/* Connect to a MySQL database using driver invocation */
	$dsn = 'mysql:dbname=maindata;host=192.168.156.236';
	$user = 'gavin';
	$password = 'gavin';
	$result = null;
	
	try {
		$dbh = new PDO($dsn, $user, $password);
		
		
		foreach ($dbh->query($sql) as $row) {
			$result= $row;
		}
		
	} catch (PDOException $e) {
		echo 'Connection failed: '.$e->getMessage();
	}
	
	return $result;
}















?>