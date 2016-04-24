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
 * Send json answer client
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

		$this->addHeader('Content-Type', 'application/json');

		parent::__construct($content, $code);

	}

	public function sendBody() {
		$json = json_encode($this->content);
		echo $json;
	}

}