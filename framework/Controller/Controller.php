<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 22:31
 */

namespace Framework\Controller;

use Framework\DI\Service;
use Framework\Request\Request;
use Framework\Response\ResponseRedirect;
use Framework\Router\Router;

/**
 * Class Controller
 *
 * @package Framework\Controller
 *
 */
abstract class Controller
{

	/**
	 * @var string
	 */
	private $nameBundle = '';

	/**
	 * @var string
	 */
	private $nameFolder = '';

	/**
	 * Controller constructor.
	 */
	public function __construct() {

		$segmentsNamespace = explode('\\', get_class($this));
		$this->nameBundle = $segmentsNamespace[0];
		$this->nameFolder = str_replace('Controller', '', end($segmentsNamespace));

	}

	/**
	 * @param $viewName
	 * @param $data
	 *
	 * @return string
	 */
	protected function render ($viewName, $data) {

		$pathView = __DIR__ . '/../../src/' . $this->nameBundle . '/views/' . $this->nameFolder . '/' . $viewName;

		return Service::get('render')->render($pathView, $data);

	}

	/**
	 * @return Request
	 */
	protected function getRequest () {

		return Service::get('request');

	}

	/**
	 * @param       $nameRoute
	 * @param array $params
	 *
	 * @return string
	 */
	protected function generateRoute ($nameRoute, array $params = []) {

		return Service::get('router')->buildRoute($nameRoute, $params);

	}

	/**
	 * @param        $url
	 * @param string $message
	 * @param string $type
	 *
	 * @return ResponseRedirect
	 */
	protected function redirect ($url, $message = '', $type = '') {

		Service::get('session')->addFlushMessage($message, $type);

		return new ResponseRedirect($url);

	}

}