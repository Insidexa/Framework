<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 05.03.16
 * Time: 10:15
 */

namespace Framework\Config;

use Framework\Helpers\Helper;
use \Exception;

/**
 * Class Config
 *
 * @package Framework\Config
 */
class Config {

	/**
	 * @var array
	 */
	private $config = [];

	/**
	 * Config constructor.
	 *
	 * @throws \Exception
	 *
	 * @param array $config
	 */
	public function __construct(array $config) {

		if (empty($config)) {
			throw new Exception('Empty config');
		}

		$this->config = $config;

	}

	/**
	 * @param string $pathToKey
	 *
	 * @return null
	 * @throws \Exception
	 */
	public function get (string $pathToKey) {

		if (empty($pathToKey)) {
			throw new \Exception('Path key empty');
		}

		return $this->preparePath($pathToKey);

	}

	/**
	 * @param string $pathToKey
	 *
	 * @return null
	 */
	private function preparePath (string $pathToKey) {

		return Helper::arrayGet($this->config, $pathToKey);

	}

}