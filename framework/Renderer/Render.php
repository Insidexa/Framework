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
<<<<<<< HEAD
use Framework\Helpers\Helper;
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556

/**
 * Class Render
 * Render template
 *
 * @package Framework\Renderer
 */
class Render {

	/**
	 * @var string
	 */
	/*private $mainLayout = 'layout.html';*/

	/**
<<<<<<< HEAD
	 * Render constructor.
	 * Set main layout
	 *
	 * @param $mainLayout
=======
	 * @var string
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	 */
	private $layoutExtension = '.php';

	public function __construct() {}

	/**
	 * Return content with main layout if passed argument 'true' or without
	 *
	 * @param      $pathView
	 * @param null $data
	 *
	 * @return null|string
	 * @throws Exception
	 */
	public function render ($pathView, $data = null ) {

		$include = function ($controller, $method, array $arguments = [] ) {

			$methodName = $method . 'Action';

			$controllerObj = new $controller;
			$controllerObj->$methodName($arguments);
		};

		$getRoute = function ($route, array $params = []) {
			return Service::get('router')->buildRoute($route, $params);
		};

		$generateToken = function () {
			return random_int(1000, 2132133);
		};

		$path = $pathView . $this->layoutExtension;

		if (!file_exists($path)) throw new Exception('File ' . $path . ' not found');

		$content = null;

		if ($data === null) {
			$content = $this->getRenderBuffer($path);
		} else {
			$content = $this->getRenderBuffer($path, $data);
		}

		return $content;

		//include($this->mainLayout . $this->layoutExtension);
	}

	/**
	 * Include template, extract variables for template
	 * initialize closures
	 * return rendered content
	 *
	 * @param               $pathView
	 * @param null|array    $data
	 *
	 * @return string
	 */
	private function getRenderBuffer ($pathView, $data = null) {

		if ($data !== null) extract($data);

		ob_start();

		include_once($pathView);

		return ob_get_clean();

	}

}