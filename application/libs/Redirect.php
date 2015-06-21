<?php

	/**
	 * 	REDIRECT
	 * 
	 * 	Shorthand for redirecting user to a specific location or default location
	 */
	class Redirect {
		
		/**
		 * 	REDIRECT USER TO A SPECIFIC LOCATION
		 */
		public static function to($location = null) {
			
			if($location) {
				
				// check if a number was passed as an argument (error code)
				if(is_numeric($location)) {
					
					// render template based on number passed in
					switch($location) {
						case 404:
							header('Location: ' . URL . 'error');
							exit();
							break;
					}
				}
				
				// otherwise take the user to the location
				header('Location: ' . $location);
				exit();
			}
		}
		
	}