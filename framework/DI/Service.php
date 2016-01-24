<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 16:01
 */

namespace Framework\DI\ServiceLocator;

use Exception\ServiceNotFound;

class Service
{

	private static $services = [];

	public static function get ($service) {
		if (array_key_exists($service, self::$services)) {
			return self::$services[$service];
		} else {
			throw new ServiceNotFound('Service ' . $service . ' not created');
		}

	}

	public static function set ($nameService, $service) {

		self::$services[$nameService] = $service;

	}

}