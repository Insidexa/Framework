<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 19.03.16
 * Time: 20:33
 */

namespace Framework\Request;

/**
 * Class RequestUri
 *
 * @package Request
 */
class RequestUri implements UriInterface {

	protected $scheme;

	protected $host;

	protected $port;

	protected $uri;

	protected $fragment;

	protected $parsedUrl;

	public function __construct() {
		$this->scheme = array_key_exists('HTTPS', $_SERVER) ? $_SERVER['HTTPS'] : 'http';
		$this->host = $_SERVER['HTTP_HOST'];
		$this->port = $_SERVER['SERVER_PORT'];
		$this->uri = $_SERVER['REQUEST_URI'];
		$this->parsedUrl = parse_url($this->getUrl());
	}

	/**
	 * @return string
	 */
	public function getUrl () {

		return $this->scheme . '://' . $this->host . $this->uri;

	}

	/**
	 * @return string
	 */
	public function getFragment() {

		return $this->parsedUrl['fragment'];

	}

	/**
	 * @return string
	 */
	public function getHost() {

		return $this->host;

	}

	/**
	 * @return string
	 */
	public function getPath() {

		return $this->parsedUrl['path'];

	}

	/**
	 * @return string
	 */
	public function getPort() {

		return $this->port;

	}

	/**
	 * @return string
	 */
	public function getQuery() {

		return $this->uri;

	}

	/**
	 * @return string
	 */
	public function getScheme() {

		return $this->scheme;

	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->getScheme() . '://' . $this->getHost();
	}

}