<?php
	class general_functions {

		/***********************************************************
		*
		*	General Functions
		*
		***********************************************************/
		
		/*
		 * HTML Encode a String
		 * Mainly used to Input Fields
		 */
		public function html_encode($str) {
			$content = '';
			if ( is_array($str) ) {
				foreach ( $str as $string ) {
					$content = htmlentities($string, ENT_QUOTES, 'UTF-8');
				}
			} else {
				$content = htmlentities($str, ENT_QUOTES, 'UTF-8');
			}
			return $content;
		}
		
		// Create HTML Attributes from an array
		public function get_html_attr ($attr = array()) {
			// If no value is passed then return blank
			if ( !isset($attr) || empty($attr) ) {return '';}
			
			$html = '';
			
			if(!empty($attr)) {
				// If the attributes are not array then return the string value
				if (!is_array($attr))
					return $attr;
					
				// Convert array to html attributes
				foreach($attr AS $key => $value) {
					// allow boolean array("checked" => true);
					if(is_bool($value)) {
						// most browsers support <.. disabled OR disabled="disabled" />
						if(!$value) continue;
						$value = $key;
					}
					
					// If the value is array then convert it to string
					if (is_array($value)) {
						// Check if array is not associative array
						if ( $this->is_associative_array($value) === false ) {
							// Non-Associative Array: class = array('class1','class2')
							$value = $this->array_to_string($value);
						} else {
							// Associative Array: style = array('width'=>'100%','display'=>'none')
							$value = $this->array_to_string_asso ( $value, '; ', ':' );
						}
					}
					
					$html .= ' '. $key .'="'. $value .'"';	// e.g. name="somename" id="someid"
				}
			}
			return trim($html);	// Trim the html and return
		}
		
		// Check if an array is associative array
		public function is_associative_array ( $array ) {
			if ( array_values($array) === $array ) {
				return false;
			} else {
				return true;
			}
		}
		
		// Convert array items to string
		public function array_to_string ($array, $splitter = " ") {
			if ( is_array($array) ) {
				$str = implode( $splitter, $array );
			} else {
				$str = $array;
			}
			return $str;
		}
		
		// Convert associative array items to string
		public function array_to_string_asso ($array, $splitter1 = " ", $splitter2 = "=" ) {
			$new_array = array();
			foreach ( $array as $key => $value ) {
				$new_array[] = $key . $splitter2 . $value;
			}
			return implode( $splitter1, $new_array);
		}
		
		// Get class string from array of classes
		public function get_classes ($classArray) {
			// If the class list is array then make it string
			$class_list = ( is_array($classArray) ) ? $this->array_to_string ($classArray) : $classArray;
			// if list is not empty then return class or return empty string
			return ( !empty($class_list) ) ? 'class="' . $class_list . '" ' : '';
		}
		
		// This function will get value from an array
		// $value accepts array which means if the value of an element matches the array's key value then only it will return the value
		public function get_value ($array, $value, $default_val = '') {
			if ( !is_array($value) ) {
				return isset( $array[$value] ) ? $array[$value] : $default_val;
			} else {
				foreach ( $value as $key => $val ) {
					return isset( $array[$key] ) ? ($array[$key] == $val ? $array[$key] : $default_val ) : $default_val;
				}
			}
		}
		
		// Get Absolute Integer value
		public function get_absint($int) {
			return abs(intval($int));
		}
		
		// Make array from string
		public function make_array($str) {
			if ( !is_array($str) ) { $str = array($str); }
			return $str;
		}
		
		public function redirectPage($pageURL){
			if ( headers_sent() ) {
				echo "<meta http-equiv='refresh' content='0;url=" . $pageURL . "'>";
			} else {
				header( "Location: " . $pageURL . "" ) ;
			}
			exit;
			die;
		}
		
		public function shortenStr ($str,$strLength,$extraDots) {
			// Get Integer
			$strLength = abs(intval($strLength));
			// Check $str length is more than $strLength
			// If yes then keep $extraDots else make $extraDots empty
			(strlen($str) > $strLength) ? $extraDots = $extraDots : $extraDots = "";
			
			// Get first $strLength characters and add $extraDots value
			$resultStr = substr($str,0,$strLength) . $extraDots;
			
			return $resultStr;
		}
	}
?>