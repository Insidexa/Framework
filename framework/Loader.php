<?php

/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 14:35
 */


/**
 * Class Loader
 */
class Loader
{
	/**
	 * @var array
	 */
	private static $prefixNamespaces = [];

	/**
	 * @var string
	 */
	private static $namespaceSeparator = '\\';

	/**
	 * @var string
	 */
	private static $fileExtension = '.php';

	/**
	 * @var
	 */
	private static $instance;

	private function __construct() {

		self::addNamespacePath('Framework\\', __DIR__ . '/../framework');

		spl_autoload_register([
			self::class,
			'loadClass'
		]);
	}

	public static function getInstance () {
		if(empty(self::$instance)) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * @param $className
	 */
	public static function loadClass ($className) {

		$segmentsNamespace = explode(self::$namespaceSeparator, $className);
		unset($segmentsNamespace[0]);
		$segmentsNamespace = implode(DIRECTORY_SEPARATOR, $segmentsNamespace);

		foreach(self::$prefixNamespaces as $namespace => $path_directory){

			$path = $path_directory . DIRECTORY_SEPARATOR . $segmentsNamespace . self::$fileExtension;
			if (file_exists($path)) {
				include_once($path);
			}
		}

	}

	/**
	 * @param $prefixNamespace
	 * @param $path
	 */
	public static function addNamespacePath ($prefixNamespace, $path) {
		self::$prefixNamespaces[$prefixNamespace] = $path;
	}

	private function __clone() {
		// TODO: Implement __clone() method.
	}

}

Loader::getInstance();