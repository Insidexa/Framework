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
use Framework\Helpers\Helper;
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

	private $mainLayout;

	/**
	 * Render constructor.
	 *
	 * @param $mainLayout
	 */
	public function __construct($mainLayout) {

		$this->mainLayout = $mainLayout;

	}

	/**
	 * @param      $pathView
	 * @param null $data
	 * @param boolean $withMain
	 *
	 * @return null|string
	 * @throws Exception
	 */
	public function render ($pathView, $data = null, $withMain = false ) {

		$path = $pathView;

		if (!strripos($pathView, $this->layoutExtension))
			$path = $pathView . $this->layoutExtension;

		if (!file_exists($path)) throw new Exception('File ' . $path . ' not found');

		$content = $this->getRenderBuffer($path, $data);

		if ($withMain) {
			$content = $this->render($this->mainLayout, [
				'content' => $content
			]);
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

		$include = Helper::include();
		$getRoute = Helper::buildRoute();
		$generateToken = Helper::getTokenField();
		$flush = Service::get('session')->getFlushMessages();

		if (strripos($pathView, 'layout')) {
			Service::get('session')->delete('flush');
		}

		$user = Service::get('security')->getUser();
		$route['_name'] = Service::get('router')->getNameRoute();

		if (Service::get('session')->get('validator.data') !== false) {
			extract(Service::get('session')->get('validator.data'));
		}

		if ($data !== null) extract($data);

		ob_start();

		include_once($pathView);

		return ob_get_clean();

	}

}