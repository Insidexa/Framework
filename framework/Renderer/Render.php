<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 02.02.16
 * Time: 23:20
 */

namespace Framework\Renderer;

use Exception;
use Framework\DI\Service;
use Framework\Router\Dispatcher;

/**
 * Class Render
 *
 * @package Framework\Renderer
 */
class Render {

	/**
	 * @var string
	 */
	private $layoutExtension = '.php';

	/**
	 * @param      $pathView
	 * @param null $data
	 *
	 * @return null|string
	 * @throws Exception
	 */
	public function render ($pathView, $data = null ) {

		$path = $pathView;

		if (!strripos($pathView, $this->layoutExtension))
			$path = $pathView . $this->layoutExtension;

		if (!file_exists($path)) throw new Exception('File ' . $path . ' not found');

		$content = null;

		if ($data === null) {
			$content = $this->getRenderBuffer($path);
		} else {
			$content = $this->getRenderBuffer($path, $data);
		}

		return $content;
	}

	/**
	 * @param               $pathView
	 * @param null|array    $data
	 *
	 * @return string
	 */
	private function getRenderBuffer ($pathView, $data = null) {

		$include = function ($controller, $method, array $arguments = [] ) {
			Dispatcher::create($controller, $method, $arguments);
		};

		$getRoute = function ($route, array $params = []) {
			return Service::get('router')->buildRoute($route, $params);
		};

		$generateToken = function () {
			$token = Service::get('security')->getToken();
			$html = '<input type="hidden" value="' . $token . '">';
			echo $html;
		};

		$flush = Service::get('session')->getFlushMessages();
		$user = Service::get('security')->getUser();
		$route['_name'] = Service::get('router')->getNameRoute();

		if ($data !== null) extract($data);

		ob_start();

		include_once($pathView);

		return ob_get_clean();

	}

}