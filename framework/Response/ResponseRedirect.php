<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 25.01.16
 * Time: 15:00
 */

namespace Framework\Response;

/**
 * Class ResponseRedirect
 *
 * @package Framework\Response
 */
class ResponseRedirect extends Response {

	/**
	 * ResponseRedirect constructor.
	 *
	 * @param     $url
	 * @param int $code
	 */
	public function __construct($url, $code = 301) {

		$this->addHeader('Location', $url);

		parent::__construct('', $code);

	}

}