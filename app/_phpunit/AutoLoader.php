<?php

/**
 *  AUTOLOADER.PHP
 *  Load all classes available in requested directories for unit testing
 *  http://jes.st/2011/phpunit-bootstrap-and-autoloading-classes/
 */
class AutoLoader {

  static private $classNames = array();

  /**
  * Store the filename (sans extension) & full path of all ".php" files found
  */
  public static function registerDirectory($dirName) {

    $di = new DirectoryIterator($dirName);
    foreach ($di as $file) {

      if ($file->isDir() && !$file->isLink() && !$file->isDot()) {

        // recurse into directories other than a few special ones
        self::registerDirectory($file->getPathName());

      } elseif (substr($file->getFileName(), -4) === '.php') {

        // save the class name / path of a .php file found
        $className = substr($file->getFileName(), 0, -4);
        AutoLoader::registerClass($className, $file->getPathName());
      }
    }
  }

  public static function registerClass($className, $fileName) {
    AutoLoader::$classNames[$className] = $fileName;
  }

  public static function loadClass($className) {
    if (isset(AutoLoader::$classNames[$className])) {
      require_once(AutoLoader::$classNames[$className]);
    }
  }

  public static function printClassNames() {
    print_r(AutoLoader::$classNames);
  }
}

spl_autoload_register(array('AutoLoader', 'loadClass'));
