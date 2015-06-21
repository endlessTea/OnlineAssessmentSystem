<?php 

	/**
	 *	CONFIG
	 * 
	 * 	Allows access to configuration details (see core/init.php) in a 
	 *  straightforward manner by passing the desired path.
	 */
	class Config {
		
		/**
		 * 	GET CONFIGURATION VALUES
		 */
		public static function get($path = null) {
			
			// if the path variable has been set
			if($path) {
				
				// use the global config array
				$config = $GLOBALS['config'];
				
				// turn the path into an array
				$path = explode('/', $path);
				
				// loop through each bit
				foreach($path as $bit) {
					
					// find the value according to the path
					if(isset($config[$bit])) {
						$config = $config[$bit];
					}
				}
				
				// return the requested value
				return $config;
			}
			
			// otherwise indicate that this was not successful
			return false;
		}
	}
