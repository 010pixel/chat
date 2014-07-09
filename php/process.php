<?php
	class my_process {
		
		var $process = NULL;
		
		var $page = array();
		
		function __construct() {
		}
		
		function check_process() {
			if ( !isset($this->process) ) {
				$this->return_values();
				return;
			}
		}
		
		function add_value ($key, $value) {
			$this->page[$key] = $value;
		}
		
		function return_values () {
			echo json_encode($this->page,JSON_PRETTY_PRINT);
			die();
		}
	}
?>