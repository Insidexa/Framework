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
	protected $header = 'Content-Type: text/html';

	/**
	 * @var int
	 */
	protected $code;

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
	public function __construct($content, $code = 200) {

		$this->code = $code;
		$this->content = $content;

		$this->send();

	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function addHeader ($name, $value) {

		$this->header = $name . ': ' . $value;

	}

	/**
	 *
	 */
	public function send () {

		http_response_code($this->code);
		header($this->header);

		echo $this->content;

	}

}