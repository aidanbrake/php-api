<?php
require_once ("index.php");

$url = "http://admin.mangomolo.com/api/index.php?key=a684eceee76fc522773286a8&action=get_vod_list";
$json_string = file_get_contents($url);
$json_data = json_decode($json_string);
echo $json_data[0]['title'];

?>
