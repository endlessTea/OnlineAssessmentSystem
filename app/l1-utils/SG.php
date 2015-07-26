<?php

/**
 *  SG.PHP
 *  Wrapper class (static) for PHP superglobal access and modification
 *  @author Jonathan Lamb
 */
class SG {

  /**
   *  $_GET
   *  Allows escaped or dangerous input to be obtained
   *
   *  Split URL method part of the 'Application' class in MINI PHP
   *  @author original: Panique
   *  @link https://github.com/panique/mini
   *  @license http://opensource.org/licenses/MIT MIT License
   *
   *  @return escaped input, array of values for controller/action/params, or unfiltered values in $_GET
   */
  public static function get($key, $usage = null) {

    if ($usage === 'escape') {

      $get = $_GET[$key];
      return htmlentities($get, ENT_QUOTES, 'UTF-8');

    } elseif ($key === 'url') {

      if (isset($_GET['url'])) {

        $response = array();

				// split URL
				$url = trim($_GET['url'], '/');
				$url = filter_var($url, FILTER_SANITIZE_URL);
				$url = explode('/', $url);

				// Put URL parts into according properties
				$response['controller'] = isset($url[0]) ? $url[0] : null;
				$response['action'] = isset($url[1]) ? $url[1] : null;

				// Remove controller and action from the split URL
				unset($url[0], $url[1]);

				// Rebase array keys and store the URL params
				$response['parameters'] = array_values($url);

        // return parsed url
        return $response;
			}

    } elseif ($usage === 'dangerous') {

      return $_GET[$key];
    }

    return 'Unrecognised usage: please specify \'escape\', \'url\' (with key of \'url\') or \'dangerous\'';
  }

  /**
   *  $_POST
   *  Allows escaped or dangerous input to be obtained
   *  @return escaped input or dangerous input from $_POST
   */
  public static function post($key, $usage = null) {

    if ($usage === 'escape') {

      $post = $_POST[$key];
      return htmlentities($post, ENT_QUOTES, 'UTF-8');

    } elseif ($usage === 'dangerous') {

      return $_POST[$key];
    }

    return 'Unrecognised usage: please specify \'escape\' or \'dangerous\'';
  }

  // SESSION - exists, put, get, delete

  /**
   *  $_SESSION
   *  Maybe reference PHP Academy for shorthand?
   */
  public static function session($name, $usage = null, $value = null) {

    if ($usage === 'exists') {

      return (isset($_SESSION[$name])) ? true : false;

    } elseif ($usage === 'put' && $value !== null) {

      $_SESSION[$name] = $value;
      return true;

    } elseif ($usage === 'get') {

      return $_SESSION[$name];

    } elseif ($usage === 'delete') {

      if (SG::session($name, 'exists')) {
      	unset($_SESSION[$name]);
        return true;
      }
    }

    return 'Unrecognised usage: please specify \'exists\', \'put\', \'get\' or \'delete\'';
  }
}
