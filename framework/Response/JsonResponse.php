<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 25.01.16
 * Time: 15:03
 */

namespace Framework\Response;

/**
 * Class JsonResponse
 *
 * @package Framework\Response
 */
class JsonResponse extends Response {

	/**
	 * JsonResponse constructor.
	 *
	 * @param     $content
	 * @param int $code
	 */
	public function __construct($content = '', $code = 200) {

		$this->code = $code;
		$this->content = $content;

		$this->addHeader('Content-Type', 'application/json');

		$this->send();
	}

	public function sendBody() {
		$json = json_encode($this->content);
		echo $json;
	}

}