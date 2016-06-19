<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "vintagestory";

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

$ip = gethostbyname('www.tyron.at');

$ipnum = sprintf("%u", ip2long($ip));

$res = mysqli_query($link, "SELECT geoname_id  FROM `countryblocksip4` WHERE `range_begin` <= {$ipnum} AND `range_end` >= {$ipnum} LIMIT 1");
if (!$res) die(mysql_error($link));
$geoid = $res->fetch_object()->geoname_id;

$res = mysqli_query($link, "SELECT country.country_name, country.continent_name from country  where geoname_id={$geoid}");
if (!$res) die(mysql_error($link));

$row = $res->fetch_object();

echo "{$ip} is located at:\r\n";
echo "Country: " . $row->country_name . "\r\n";
echo "Continent: " . $row->continent_name . "\r\n";

