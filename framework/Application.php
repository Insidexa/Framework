<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 14:42
 */

namespace Framework;

use Framework\DI\Service;
use Framework\Request\Request;
use Framework\Router\Router;
use Framework\Exception\HttpNotFoundException;

class Application
{

	/**
	 * @var array
	 */
	private $config = [];

	/**
	 * Application constructor.
	 *
	 * @param $config
	 */
	public function __construct($config) {
		$this->config = include ($config);
	}

	public function run () {

		$this->createServices();
		$map = Service::get('router')->getMap();

		var_dump($map);

		if ($map['method'] === 'notFound') {
			$this->notFound();
		}

	}

	private function createServices () {

		Service::set('request', new Request());
		Service::set('router', new Router($this->config['routes']));

	}

	private function notFound () {
		throw new HttpNotFoundException('Page not found');
	}

}