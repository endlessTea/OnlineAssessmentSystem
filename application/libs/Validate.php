<?php

	/**
	 * 	VALIDATE
	 * 
	 * 	Validation class used to simplify checks of values
	 */
	class Validate {
		
		// instance variables
		private $_passed = false,
				$_errors = array(),
				$_db = null;
		
		/**
		 *	CONSTRUCTOR: establish private connection to database
		 */
		public function __construct() {
			$this->_db = DB::getInstance();
		}
		
		/**
		 *	CHECK: pass the data to check and a set of rules
		 */
		public function check($source, $items = array()) {
			
			// loop through the rules provided
			foreach($items as $item => $rules) {
				foreach($rules as $rule => $rule_value) {
					
					// set the value and trim any leading or trailing spaces
					$value = trim($source[$item]);
					$item = Functions::escape($item);
					
					// check if the value is required or not
					if($rule === 'required' && empty($value)) {
						$this->addError("{$item} is required.");		// $item is going to equal a field name of the value
					} else if(!empty($value)) {
						
						// create a case for each of the rules you want to check
						switch($rule) {
							
							// assert a minimum length: add an error if the string length is less than the minimum rule
							case 'min':
								if(strlen($value) < $rule_value) {
									$this->addError("{$item} must be a minimum of {$rule_value} characters.");
								}
								break;
								
							// assert a maximum length: add an error if the string length is more than the maximum rule
							case 'max':
								if(strlen($value) > $rule_value) {
									$this->addError("{$item} must be a maximum of {$rule_value} characters.");
								}
								break;
								
							// assert value matches another value from source data (POST/GET data)
							case 'matches':
								if($value != $source[$rule_value]) {
									$this->addError("{$rule_value} must match {$item}.");
								}
								break;
								
							// assert a value does not already exist in the database
							case 'unique':
							
								$check = $this->_db->query(
									"SELECT * FROM `{$rule_value}` WHERE `{$item}` = '{$value}';"
								)->count();
								
								if($check > 0) {
									$this->addError("{$item} already exists.");
								}
								break;
						}
					}
				}
			}
			
			// if there are no errors to report, pass the validation
			if(empty($this->_errors)) {
				$this->_passed = true;
			}
			
			return $this;
		}
		
		/**
		 *	ADD ERRORS: append an error to the Validation object
		 */
		private function addError($error) {
			$this->_errors[] = $error;
		}
		
		/**
		 *	RETURN ERRORS
		 */
		public function errors() {
			return $this->_errors;
		}
		
		/**
		 *	RETURN INDICATION OF WHETHER OR NOT VALIDATION PASSED
		 */
		public function passed() {
			return $this->_passed;
		}
	}
