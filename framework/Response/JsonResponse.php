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
	 * @var string
	 */
	protected $header = 'Content-Type: application/json';

	/**
	 * JsonResponse constructor.
	 *
	 * @param     $content
	 * @param int $code
	 */
	public function __construct($content, $code = 200) {

		$this->code = $code;

		$json = json_encode($content);
		http_response_code($this->code);
		header($this->header);
		echo $json;
	}

}