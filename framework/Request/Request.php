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
 * Manipulation of url, form data
 *
 * @author Jashka
 *
 * @package Framework\Request
 */
class Request implements RequestInterface {

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
	private $clientIp = null;

	/**
	 * @var null
	 */
	private $scheme = null;

	/**
	 * @var array
	 */
	private $headers = [];

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
		$this->headers = $this->parseHeaders(headers_list());
	}

	// TODO: add class for upload file and implement psr7 UploadedFileInterface
	public function getFile ($name) {
		return $this->files[$name];
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
			: null;
	}

	/**
	 * @return string
	 */
	public function getClientAddr () {
		return $this->clientIp;
	}

	/**
	 * Return current url with scheme
	 *
	 * @return string
	 */
	public function getUrl () {
		return $this->scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	/**
	 * @param string $name
	 *
	 * @return null
	 */
	public function getHeader($name) {
		return array_key_exists($name, $this->headers) ? $this->headers[$name] : null;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasHeader($name) {
		return array_key_exists($name, $this->headers);
	}

	/**
	 * @param $headers
	 *
	 * @return array
	 */
	protected function parseHeaders ($headers) {

		$parsedHeaders = [];

		foreach ($headers as $header) {

			$data = explode(':', $header);
			$parsedHeaders[$data[0]] = $data[1];

		}

		return $parsedHeaders;

	}

	/**
	 * @return array|false
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * @return string
	 */
	public function getProtocolVersion() {
		return $_SERVER['SERVER_PROTOCOL'];
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
	 * Return type method
	 *
	 * @return null
	 */
	public function getMethod () {
		return $this->method;
	}

	public function getStringUri () {

		return (new RequestUri())->getQuery();

	}

	/**
	 * @return null
	 */
	public function getUri () {
		return new RequestUri();
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
	protected function filterRequest ($data) {
		return filter_var($data, FILTER_SANITIZE_STRING);
	}

}