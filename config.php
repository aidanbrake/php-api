<?php
	$env = "development";

	if ($env == 'product') {
		$hostname = 'localhost';
		$db_username = 'admmang_abhi';
		$password = 'Abhi@123';
		$dbname = 'admmang_analytics';
	} else {
		$hostname = 'localhost';
		$db_username = 'root';
		$password = 'root';
		$dbname = 'testdb';
	}


?>