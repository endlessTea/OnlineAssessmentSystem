<?php 

	/**
	 *	DB
	 * 
	 * 	Support connections to a database via a wrapper class.
	 * 	Allow queries and fetching results/count in an object oriented way.
	 * 	Only allows a single connection to be established throughout the application.
	 */
	class DB {
		
		// establish database connection as a singleton: avoids needing to reconnect to the database regularly
		private static $_instance = null;
		
		// private instance variables
		private $_pdo, 
				$_query, 
				$_error = false, 
				$_results, 
				$_count = 0;
		
		/**
		 *	PRIVATE CONSTRUCTOR
		 */
		private function __construct() {
			try {

				// set the (optional) options of the PDO connection
        		$options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);

				// establish (private) PDO connection
				$this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . 
									  ';dbname=' . Config::get('mysql/db'),
									  Config::get('mysql/username'),
									  Config::get('mysql/password'), 
									  $options);

			} catch(PDOException $e) {
				die($e->getMessage());
			}
		}
		
		/**
		 *	GET INSTANCE: allow only one DB object to be created.
		 */
		public static function getInstance() {
			
			// check if instance has not yet been instantiated first
			if(!isset(self::$_instance)) {
				self::$_instance = new DB();
			}
			
			return self::$_instance;
			
		}
		
		/**
		 *	QUERY: perform SQL queries, storing results, count and indication
		 *	of errors as part of the Database singleton.
		 */
		public function query($sql) {
			
			// initialise errors to false
			$this->_error = false;
			
			// prepare SQL query, return object in case there are any problems
			if($this->_query = $this->_pdo->prepare($sql)) {
				
				// execute the query; store the result set
				if($this->_query->execute()) {
					
					// only fetch results if a SELECT query was requested
					if (strpos($sql,'SELECT') !== false) {
						$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
					}
					
					// retrieve the row count
					$this->_count = $this->_query->rowCount();
					
				} else {
					// indicate that there has been an error attempting to query the database
					$this->_error = true;
				}
				
			}
			
			// return the current object we're working with; allows other methods to be chained on to it, such as error()
			return $this;
		}
		
		/**
		 *	Return the results
		 */
		public function results() {
			return $this->_results;
		}
		
		/**
		 *	Return the first result
		 */
		public function first() {
			
			// call the results as well function to obtain the first result
			return $this->results()[0];
		}
		
		/**
		 *	Return an indication of whether or not an error occured (bool)
		 */
		public function error() {
			return $this->_error;
		}
		
		/**
		 *	Return the number of results from the last query
		 */
		public function count() {
			return $this->_count;
		}
	}
