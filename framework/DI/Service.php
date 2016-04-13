<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 16:01
 */

namespace Framework\DI;

use Framework\Exception\ServiceNotFoundException;

<<<<<<< HEAD
/**
 * Class Service
 * Service Locator for objects
 *
 * @package Framework\DI
 */
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
class Service {

	/**
	 * @var array
	 */
	private static $services = [];


	/**
	 * @param $service
	 *
	 * @return mixed
	 */
	public static function get($service) {
		return self::issetService($service);
	}

	/**
	 * @param $nameService
	 * @param $service
	 */
	public static function set($nameService, $service) {
		self::$services[ $nameService ] = $service;
	}

	/**
	 * @param $service
	 *
	 * @return mixed
	 * @throws ServiceNotFoundException
	 */
	private function issetService($service) {
		return array_key_exists($service, self::$services)
			? self::$services[ $service ]
			: self::$services[ $service ] = $this->createService($service);
	}

	/**
	 * @param $service
	 *
	 * @return mixed
	 */
	public function __get($service) {
		return $this->issetService($service);
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function __set($name, $value) {
		self::set($name, $value);
	}

	/**
	 * @param $className
	 *
	 * @return mixed
	 * @throws ServiceNotFoundException
	 */
	private function createService($className) {
		if (class_exists($className))
			return new $className;

		throw new ServiceNotFoundException('Service ' . $className . ' not created');
	}


}