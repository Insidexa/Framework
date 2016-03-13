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
 * Manipulation of form data
 *
 * @author Jashka
 *
 * @package Framework\Request
 */
class Request {

	/**
	 * @var array
	 */
	private $post = [];

	/**
	 * @var array
	 */
	private $get = [];

	/**
	 * @var array
	 */
	private $files = [];

	/**
	 * @var int|null
	 */
	private $code = null;

	/**
	 * @var null
	 */
	private $method = null;

	/**
	 * @var null
	 */
	private $timeRequest = null;

	/**
	 * @var null
	 */
	private $clientIp = null;

	/**
	 * @var null
	 */
	private $scheme = null;

	/**
	 * @var null
	 */
	private $host = null;

	/**
	 * @var null
	 */
	private $uri = null;

	/**
	 * Request constructor.
	 */
	public function __construct() {
		$this->get = $_GET;
		$this->post = $_POST;
		$this->files = $_FILES;
		$this->code = http_response_code();
		$this->clientIp = $_SERVER['SERVER_ADDR'];
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->timeRequest = $_SERVER['REQUEST_TIME'];
		$this->scheme = (isset($_SERVER['HTTPS'])) ?? 'http';
		$this->host = $_SERVER['HTTP_HOST'];
		$this->uri = $_SERVER['REQUEST_URI'];
	}

	/**
	 * Return GET input data for name key
	 *
	 * @param $nameKey
	 *
	 * @return string
	 */
	public function get($nameKey) {
		return array_key_exists($nameKey, $this->get)
			? $this->filterRequest($this->get[ $nameKey ])
			: 'NULL';
	}

	/**
	 * Return POST input data for name key
	 *
	 * @param $nameKey
	 *
	 * @return string
	 */
	public function post($nameKey) {
		return array_key_exists($nameKey, $this->post)
			? $this->filterRequest($this->post[ $nameKey ])
			: null;
	}

	/**
	 * Return code from header
	 *
	 * @return int|null
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * Return true if POST request
	 *
	 * @return bool
	 */
	public function isPost() {
		return $this->method === 'POST';
	}

	/**
	 * Return true if GET request
	 *
	 * @return bool
	 */
	public function isGet() {
		return $this->method === 'GET';
	}

	/**
	 * Return true if PUT request
	 *
	 * @return bool
	 */
	public function isPut() {
		return $this->method === 'PUT';
	}

	/**
	 * Return true if ajax request from client
	 *
	 * @return bool
	 */
	public function isAjax () {
		$flag = false;
		if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
			$flag = true;
		}
		return $flag;
	}

	/**
	 * Return current url with scheme
	 *
	 * @return string
	 */
	public function getUrl () {
		return $this->scheme . '://' . $this->host . $this->uri;
	}

	/**
	 * Return type method
	 *
	 * @return null
	 */
	public function getMethod () {
		return $this->method;
	}

	/**
	 * @return null
	 */
	public function getUri () {
		return $this->uri;
	}

	/**
	 * @return bool|null|string
	 */
	public function getScheme () {
		return $this->scheme;
	}

	/**
	 * Return cleared input data without
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	private function filterRequest ($data) {
		return filter_var($data, FILTER_SANITIZE_STRING);
	}

}