<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 22:31
 */

namespace Framework\Controller;

use Framework\DI\Service;
use Framework\Helper\Helper;
use Framework\Request\Request;
use Framework\Response\Response;
use Framework\Response\ResponseRedirect;

/**
 * Class Controller
 * Base Controller
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

		$data = Helper::getDataClass(get_class($this));

		$this->nameFolder = $data['nameFolder'];
		$this->nameBundle = $data['nameBundle'];

	}

	/**
	 * @param $viewName
	 * @param $data
	 *
	 * @return string
	 */
	protected function render ($viewName, $data) {

		$pathView = Helper::viewPath($this->nameBundle, $this->nameFolder, $viewName);

		return new Response(Service::get('render')->render($pathView, $data, true));

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