<?php 

	/**
	 * 	INPUT
	 * 
	 *  Simplifies obtaining GET/POST data
	 */
	class Input {
		
		/**
		 * 	CHECK IF POST/GET DATA EXISTS
		 */
		public static function exists($type = 'post') {
			
			switch($type) {
				case 'post':
					return (!empty($_POST)) ? true : false;
					break;
					
				case 'get':
					return (!empty($_GET)) ? true : false;
					break;
					
				default:
					return false;
					break;
			}
		}
		
		/**
		 * 	GET ELEMENTS SUBMITTED VIA GET/POST
		 */
		public static function get($item) {
			
			// check POST first, then if the item isn't there, check GET
			if(isset($_POST[$item])) {
				return $_POST[$item];
			} else if(isset($_GET[$item])) {
				return $_GET[$item];
			}
			
			// return empty string by default if data doesn't exist, prevents errors occuring
			return '';
		}
	}
