<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 04.02.16
 * Time: 12:08
 */

namespace Framework\Router;

/**
 * Class Dispatcher
 *
 * @package Framework\Router
 */
class Dispatcher {

	/**
	 * @param      $controller
	 * @param      $method
	 * @param null $arguments
	 *
	 * @return mixed
	 * @throws \ErrorException | \BadMethodCallException
	 */
	public static function create ($controller, $method, $arguments = null) {

		$methodName = $method . 'Action';

		if (!class_exists($controller)) {
			throw new \ErrorException('Class ' . $controller . ' not found');
		}

		$controllerObj = new $controller;

		if (!method_exists($controllerObj, $methodName)) {
			throw new \BadMethodCallException('Method ' . $methodName . ' not found in ' . $controller);
		}

		return $controllerObj->$methodName($arguments);

	}

}