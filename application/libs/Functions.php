<?php

/**
 * 	FUNCTIONS
 *
 * 	Helper class containing misc functions
 */
class Functions {

	/**
	 *	ESCAPE A STRING
	 */
	public static function escape($string) {
		return htmlentities($string, ENT_QUOTES, 'UTF-8');
	}

	/**
	 *	RENDER THE VIEW: Load a specific header, template and footer based on the Controller and Method name (View)
	 */
	public static function render($controller, $view, $resources = array()) {

		// extract resources into local context (must be an associative array)
		extract($resources);

		// use spefic headers/footers (i.e. CSS and JavaScript libraries for different controllers/views)
		switch($controller) {

			case 'Visualisation':

				if ($view == 'sample1') {
					require APP . 'view/_templates/header_vis_sample1.php';
					require APP . 'view/' . strtolower($controller) . '/' .
						strtolower($view) . '.php';
					require APP . 'view/_templates/footer_vis_sample1.php';
					break;
				}

			default:
				require APP . 'view/_templates/header.php';
				require APP . 'view/' . strtolower($controller) . '/' .
					strtolower($view) . '.php';
				require APP . 'view/_templates/footer.php';
		}
	}

}
