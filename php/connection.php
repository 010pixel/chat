<?php
	include("db.php");
	$db = new My_DB('root', 'password', 'chat', 'localhost');
	
	if ( !$db ) {
		 die('Could not connect: ' . mysql_error());
	}
?>