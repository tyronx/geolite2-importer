<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "vintagestory";
$pathsep = "/";
$csvpath = str_replace("\\", "/", getcwd()) . "{$pathsep}csvdata";


echo "\r\nRunning import\r\n";


$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$result = importSQL("schema.sql");
if (!$result['success']) {
	echo $result['success'];
	exit();
}

$success = mysqli_query($link, "
	load data local infile '{$csvpath}{$pathsep}GeoLite2-Country-Blocks-IPv4.csv'
	into table countryblocksip4 
	fields terminated by ',' 
	LINES TERMINATED BY '\n' 
	IGNORE 1 ROWS
");

if (!$success) {
	echo mysqli_error($link);
	die();
}

$success = mysqli_query($link, "
	load data local infile '{$csvpath}{$pathsep}GeoLite2-Country-Locations-en.csv'
	into table country
	fields terminated by ',' 
	ENCLOSED BY '\"'
	LINES TERMINATED BY '\n' 
	IGNORE 1 ROWS
");

if (!$success) {
	echo mysqli_error($link);
	die();
}


$success = mysqli_query($link, "alter table countryblocksip4 add range_begin bigint default 0 after is_satellite_provider");
$success = mysqli_query($link, "alter table countryblocksip4 add range_end bigint default 0 after range_begin");

if (!$success) {
	echo mysqli_error($link);
	die();
}

$success = mysqli_query($link, "ALTER TABLE  `vintagestory`.`countryblocksip4` ADD INDEX  `range_begin` (  `range_begin` )");
$success = mysqli_query($link, "ALTER TABLE  `vintagestory`.`countryblocksip4` ADD INDEX  `range_end` (  `range_end` )");

if (!$success) {
	echo mysqli_error($link);
	die();
}

$success = mysqli_query($link, "
	UPDATE `countryblocksip4` set
		range_begin = inet_aton(SUBSTRING(network, 1, LOCATE('/', network) - 1)), 
		range_end = (inet_aton(SUBSTRING(network, 1, LOCATE('/', network) - 1)) + (pow(2, (32-cast(SUBSTRING(network, LOCATE('/', network) + 1) as signed)))-1))
");

if (!$success) {
	echo mysqli_error($link);
	die();
}



mysqli_close($link);


echo "Script done.\r\n";

function importSQL($filename) {
	global $link;
		
	$sql = file($filename);
	
	$statement='';
	foreach ($sql as $line) {
		if (preg_match("/^--/", $line)) continue;
		
		$statement.=$line;
		
		if (preg_match("/;\r\n$/",$line)) {
			
			if (!@mysqli_query($link, $statement)) {
				return array(
					"success" => false,
					"message" => "An error occurred during execution: ".mysqli_error($link)
				);
				break;
			} else $statement='';
		}
	}
	
	return array(
		"success" => true,
		"message" => "File imported"
	);
}