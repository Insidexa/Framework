<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 25.01.16
 * Time: 15:03
 */

namespace Framework\Response;

<<<<<<< HEAD
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
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556

class JsonResponse {

}