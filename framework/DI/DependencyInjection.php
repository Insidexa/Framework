<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 06.03.16
 * Time: 18:57
 */

namespace Framework\DI;

/**
 * Class DependencyInjection
 *
 * @package Framework\DI
 */
class DependencyInjection {

	private $controller;

	private $method;

	private $arguments;

	/**
	 * DependencyInjection constructor.
	 *
	 * @param $controller
	 * @param $method
	 * @param $arguments
	 */
	public function __construct($controller, $method, $arguments) {

		$this->controller = $controller;
		$this->method = $method;
		$this->arguments = $arguments;

		$this->autoInjector();

	}

	/**
	 * Call injector for constructor class and method
	 */
	public function autoInjector () {

		$this->autoInjectConstructor();
		$this->autoInjectMethod();

	}

	/**
	 * Return method arguments
	 *
	 * @return mixed
	 */
	public function getArguments () {

		return $this->arguments;

	}

	/**
	 * Return instance controller
	 *
	 * @return mixed
	 */
	public function getController () {

		return $this->controller;

	}

	/**
	 * Iterates parameters method or constructor and create class if parameter object
	 * return created arguments
	 * if argument string or array -> skip
	 *
	 * @param $type
	 * @param $parameters
	 * @param $numberParameters
	 *
	 * @return array|null
	 * @throws \Exception
	 */
	private function getDataArguments ($type, $parameters, $numberParameters) {

		$reservedArguments = null;

		switch($type) {
			case 'constructor':
				$reservedArguments = [];
				break;

			case 'method':
				$reservedArguments = $this->arguments;
				break;
			default:
				throw new \Exception('Dependency Injection bad type method');
				break;
		}

		if ($numberParameters > 0) {

			foreach ($parameters as $param) {
				$type = $param->getType();
				if (is_object($type)) {
					$nameObject = $param->getClass()->name;
					$obj = new $nameObject;
					$reservedArguments[$param->name] = $obj;
				}
			}
		}

		return $reservedArguments;

	}

	/**
	 * Create object class, Depending on the count arguments
	 * create object without arguments
	 * or passed arguments for constructor
	 *
	 * @throws \Exception
	 */
	private function autoInjectConstructor () {

		$reflectionClass = new \ReflectionClass($this->controller);
		$parameters = $reflectionClass->getConstructor()->getParameters();
		$countParams = $reflectionClass->getConstructor()->getNumberOfParameters();
		$arguments = $this->getDataArguments('constructor', $parameters, $countParams);

		if ($countParams === 0) {
			$this->controller = new $this->controller;
		}

		if ($countParams > 0) {
			$class = new \ReflectionClass($this->controller);
			$this->controller = $class->newInstanceArgs( $arguments);
		}

	}

	/**
	 * @throws \Exception
	 */
	private function autoInjectMethod () {

		$reflectionMethod = new \ReflectionMethod($this->controller, $this->method);
		$arguments = $this->getDataArguments('method', $reflectionMethod->getParameters(), $reflectionMethod->getNumberOfParameters());

		$this->arguments = $arguments;

	}

}