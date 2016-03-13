<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 04.02.16
 * Time: 12:08
 */

namespace Framework\Router;
use Framework\DI\DependencyInjection;
use Framework\Exception\ClassNotFound;
use Framework\Response\Response;

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

		if (!method_exists($controller, $methodName)) {
			throw new \BadMethodCallException('Method ' . $methodName . ' not found in ' . $controller, 500);
		}

		$di = new DependencyInjection($controller, $methodName, $arguments);
		$controllerObj = $di->getController();
		$reservedArguments = $di->getArguments();

		$reflectionMethod = new \ReflectionMethod($controller, $methodName);

		if (gettype($arguments) === 'array')
			$response = $reflectionMethod->invokeArgs($controllerObj, $reservedArguments);

		if (gettype($arguments) === 'string')
			$response = $reflectionMethod->invoke($controllerObj, $reservedArguments);

		return $response;

	}

}