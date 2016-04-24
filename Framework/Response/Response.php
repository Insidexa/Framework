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
 * Answer client
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
	 * Set content and code
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
	 * Add header
	 *
	 * @param $name
	 * @param $value
	 */
	public function addHeader ($name, $value) {

		$this->headers[$name] = $value;

	}

	/**
	 * Set header for answer
	 */
	protected function sendHeader () {
		header($_SERVER['SERVER_PROTOCOL'] . ' ' . $this->code);
		foreach($this->headers as $key => $value) {
			header(sprintf('%s: %s', $key, $value));
		}
	}

	/**
	 * Send answer client
	 */
	protected function sendBody () {
		echo $this->content;
	}

	/**
	 * Send header and body
	 */
	public function send () {

		$this->sendHeader();
		$this->sendBody();

	}

}