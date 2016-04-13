<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 16:01
 */

namespace Framework\DI;

use Framework\Exception\ServiceNotFoundException;

/**
 * Class Service
 * Service Locator for objects
 *
 * @package Framework\DI
 */
class Service {

	/**
	 * @var array
	 */
	public static $services = [];

	/**
	 * Service constructor.
	 */
	private function __construct() {
	}

	/**
	 *
	 */
	private function __clone() {
		// TODO: Implement __clone() method.
	}

	/**
	 * @param $service
	 *
	 * @return mixed
	 * @throws ServiceNotFoundException
	 */
	public static function get($service) {
		return self::issetService($service);
	}

	/**
	 * @param $nameService
	 * @param $service
	 */
	public static function set($nameService, $service) {

		if (is_object($service) || is_callable($service)) {
			self::$services[ $nameService ] = $service;
		}
	}

	/**
	 * @param $service
	 *
	 * @return mixed
	 * @throws ServiceNotFoundException
	 */
	private static function issetService($service) {

		if (array_key_exists($service, self::$services)) {
			return self::$services[ $service ];
		}

		throw new ServiceNotFoundException('Service ' . $service . ' not found');
	}

	/**
	 * @param $service
	 *
	 * @return mixed
	 */
	public function __get($service) {
		return self::issetService($service);
	}

	/**
	 * @param string $name
	 * @param object $value
	 */
	public function __set($name, $value) {
		self::set($name, $value);
	}

}