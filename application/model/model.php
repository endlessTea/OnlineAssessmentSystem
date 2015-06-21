<?php

class Model
{
    // establish Model as a singleton
    private static $_instance = null;
    
    // store private connection to database
    private $_db;
    
    /**
     * 	Private Constructor (use factory method 'getInstance' instead)
     */
    private function __construct($db)
    {
        $this->_db = $db;
    }
    
    /**
	 *	GET INSTANCE
	 *	Allow only one Model to be created
	 */
	public static function getInstance() {
		
		// check if instance has not yet been instantiated first
		if(!isset(self::$_instance)) {
			self::$_instance = new Model(DB::getInstance());
		}
		
		return self::$_instance;
	}
	
}
