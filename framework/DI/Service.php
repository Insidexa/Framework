<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 16:01
 */

namespace Framework\DI;

use Framework\Exception\ServiceNotFoundException;

class Service
{

	private static $services = [];

	public static function get ($service) {
		if (array_key_exists($service, self::$services)) {
			return self::$services[$service];
		} else {
			throw new ServiceNotFoundException('Service ' . $service . ' not created');
		}

	}

	public static function set ($nameService, $service) {

		self::$services[$nameService] = $service;

	}

}