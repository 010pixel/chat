<?php
	if ( isset($_SESSION['user']['timezone']) && $_SESSION['user']['timezone'] != "" ) {
		date_default_timezone_set($_SESSION['user']['timezone']);
	}
?>