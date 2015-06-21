<?php

	/**
	 *	APPLICATION
	 * 
	 * 	Determines which Controller and Action to use based on the URL.
	 */
	class Application
	{
		// instance variables for the requested controller, action (method) and additional URL parameters
		private $url_controller = null;
		private $url_action = null;
		private $url_params = array();

		/**
		 * 	"START THE APPLICATION"
		 * 	Analyze the URL elements and calls the according controller/method or the fallback
		 */
		public function __construct()
		{
			// create array with URL parts in $url
			$this->splitUrl();

			// check for user login removed
			
			// if a Controller was not provided, load the index of the Home controller
			if (!$this->url_controller) {

				// check for controller: no controller given ? then load start-page
				require APP . 'controller/home.php';
				$page = new Home();
				$page->index();

			// otherwise check if the requested Controller exists
			} elseif (file_exists(APP . 'controller/' . $this->url_controller . '.php')) {

				// if so, then load this file and create this controller
				require APP . 'controller/' . $this->url_controller . '.php';
				$this->url_controller = new $this->url_controller();

				// check for the requested Action (method) in the Controller
				if (method_exists($this->url_controller, $this->url_action)) {

					// if additional URL parameters are provided
					if (!empty($this->url_params)) {
						
						// Call the method and pass arguments to it
						call_user_func_array(array($this->url_controller, $this->url_action), $this->url_params);
						
					} else {
						
						// If no parameters are given, just call the method without parameters
						$this->url_controller->{$this->url_action}();
					}

				} else {
					
					// if no Action was provided, load the Controller's index page
					if (strlen($this->url_action) == 0) {
						$this->url_controller->index();
					}
					else {
						
						// an invalid Action was requested - load the 404 page not found view
						Redirect::to(404);
					}
				}
			} else {
				
				// an invalid Controller was requested - load the 404 page not found view
				Redirect::to(404);
			}
		}

		/**
		 * 	SPLIT THE URL PROVIDED
		 */
		private function splitUrl()
		{
			if (isset($_GET['url'])) {

				// split URL
				$url = trim($_GET['url'], '/');
				$url = filter_var($url, FILTER_SANITIZE_URL);
				$url = explode('/', $url);

				// Put URL parts into according properties
				$this->url_controller = isset($url[0]) ? $url[0] : null;
				$this->url_action = isset($url[1]) ? $url[1] : null;

				// Remove controller and action from the split URL
				unset($url[0], $url[1]);

				// Rebase array keys and store the URL params
				$this->url_params = array_values($url);
			}
		}
	}
