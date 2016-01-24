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


	public static function register () {
		spl_autoload_register([
			self::class,
			'loadClass'
		]);
	}

	/**
	 * @param $className
	 */
	public static function loadClass ($className) {

		$parts = explode('\\', $className);

		$file_name = end($parts).self::$fileExtension;

		$files = [];

		foreach(self::$prefixNamespaces as $namespace => $path_directory){

			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($path_directory),
				RecursiveIteratorIterator::SELF_FIRST
			);

			foreach ($iterator as $fileObject) {
				if ($fileObject->isDir()) {
					$files[] = str_replace(self::$namespaceSeparator, '/', $fileObject->getPathname()).'/';
				}
			}

		}

		$array_directories = array_merge(self::$prefixNamespaces,$files);

		foreach($array_directories as $path_directory){
			if(file_exists($path_directory.$file_name)){
				include_once $path_directory.$file_name;
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

}

