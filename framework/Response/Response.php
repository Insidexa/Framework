<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 22:41
 */

namespace Framework\Response;

/**
 * Class Response
 *
 * @package Framework\Response
 */
class Response
{

	/**
	 * @var array
	 */
	protected $headers = [
		'Content-Type' => 'text/html'
	];

	/**
	 * @var int
	 */
	protected $code = 200;

	/**
	 * @var string
	 */
	protected $content = '';

	/**
	 * Response constructor.
	 *
	 * @param $content
	 * @param $code
	 */
	public function __construct($content = '', $code = 200) {

		$this->code = $code;
		$this->content = $content;

		$this->send();

	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function addHeader ($name, $value) {

		$this->headers[$name] = $value;

	}

	protected function sendHeader () {
		header($_SERVER['SERVER_PROTOCOL'] . ' ' . $this->code);
		foreach($this->headers as $key => $value) {
			header(sprintf('%s: %s', $key, $value));
		}
	}

	protected function sendBody () {
		echo $this->content;
	}

	/**
	 *
	 */
	public function send () {

		$this->sendHeader();
		$this->sendBody();

	}

}