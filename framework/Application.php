<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 14:42
 */

use Framework\DI\Service;

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
		$this->config = $config;
	}

	public function run () {

	}

}