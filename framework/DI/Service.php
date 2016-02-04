<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 16:01
 */

namespace Framework\DI;

use Framework\Exception\ServiceNotFoundException;

class Service {

	/**
	 * @var array
	 */
	public static $services = [];


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
		return $this->issetService($service);
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function __set($name, $value) {
		self::$services[ $name ] = $value;
	}

}