<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 04.02.16
 * Time: 12:08
 */

namespace Framework\Router;
use Framework\Exception\ClassNotFound;

/**
 * Class Dispatcher
 *
 * @package Framework\Router
 */
class Dispatcher {

	/**
	 * @param       $controller
	 * @param       $method
	 * @param array $arguments
	 *
	 * @return mixed
	 * @throws ClassNotFound | \BadMethodCallException
	 */
	public static function create ($controller, $method, $arguments) {

		$methodName = $method . 'Action';

		if (!class_exists($controller)) {
			throw new ClassNotFound('Class ' . $controller . ' not found', 500);
		}

		$controllerObj = new $controller;

		if (!method_exists($controllerObj, $methodName)) {
			throw new \BadMethodCallException('Method ' . $methodName . ' not found in ' . $controller, 500);
		}

		$reflectionMethod = new \ReflectionMethod($controller, $methodName);

		if (gettype($arguments) === 'array')
			$response = $reflectionMethod->invokeArgs($controllerObj, $arguments);

		if (gettype($arguments) === 'string')
			$response = $reflectionMethod->invoke($controllerObj, $arguments);

		return $response;

	}

}