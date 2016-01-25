<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 22:39
 */

namespace Framework\Request;

/**
 * Class Request
 *
 * @autor Jashka
 *
 * @package Framework\Request
 */
class Request
{

	/**
	 * @var array
	 */
	private $post = [];

	/**
	 * @var array
	 */
	private $get = [];

	/**
	 * @var int|null
	 */
	private $code = null;

	/**
	 * @var null
	 */
	private $method = null;

	public function __construct() {
		$this->get = $_GET;
		$this->post = $_POST;
		$this->code = http_response_code();
		$this->method = $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * @param $nameKey
	 *
	 * @return string
	 */
	public function get ($nameKey) {
		return array_key_exists($nameKey, $this->get)
			? $this->get[$nameKey]
			: 'NULL';
	}

	/**
	 * @param $nameKey
	 *
	 * @return string
	 */
	public function post ($nameKey) {
		return array_key_exists($nameKey, $this->post)
			? $this->post[$nameKey]
			: 'NULL';
	}

	/**
	 * @return int|null
	 */
	public function getCode () {
		return $this->code;
	}

	/**
	 * @return bool
	 */
	public function isPost () {
		return $this->method === 'POST';
	}

	/**
	 * @return bool
	 */
	public function isGet () {
		return $this->method === 'GET';
	}

	/**
	 * @return bool
	 */
	public function isPut () {
		return $this->method === 'PUT';
	}

}