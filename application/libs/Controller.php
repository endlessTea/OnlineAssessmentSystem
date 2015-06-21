<?php

	/**
	 *	CONTROLLER
	 * 
	 * 	Parent class for specific Controllers, such as 'Home'.
	 * 	Creates a reference to the Model each time it is used (as a sub-class)
	 */
	class Controller
	{
		// load the model for each instance of a Controller
		protected $model = null;

		/**
		 * 	CONSTRUCTOR
		 */
		function __construct()
		{
			$this->loadModel();
		}
		
		/**
		 * 	LOAD THE MODEL
		 */
		private function loadModel()
		{
			require APP . 'model/model.php';
			$this->model = Model::getInstance();
		}
	}
