<?php

/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 14:35
 */

class Loader
{
	private static $prefixNamespaces = [];
	private static $path = NULL;

	public function register () {
		spl_autoload_register(array($this,'loadClass'));
	}

	public static function addNamespacePath ($prefixNamespace, $path) {
		self::$prefixNamespaces[] = $prefixNamespace;
		self::$path = $path;
	}

}

$loader = new Loader();
$loader->register();