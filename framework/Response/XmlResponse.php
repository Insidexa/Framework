<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 05.03.16
 * Time: 18:36
 */

namespace Framework\Response;

/**
 * Class XmlResponse
 *
 * @package Framework\Response
 */
class XmlResponse extends Response {

	/**
	 * XmlResponse constructor.
	 *
	 * @param string $content
	 * @param int    $code
	 */
	public function __construct($content = '', $code = 200) {

		$this->addHeader('Content-Type', 'text/xml');

		parent::__construct($content, $code);

	}

	public function sendBody() {
		echo $this->content;
	}

}