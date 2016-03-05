<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 28.02.16
 * Time: 21:03
 */

namespace Framework\Helpers;

use Framework\DI\Service;
use Framework\Router\Dispatcher;

/**
 * Class Helper
 *
 * @package Framework\Helpers
 */
class Helper {

	/**
	 * @param $nameBundle
	 * @param $nameFolder
	 * @param $viewName
	 *
	 * @return string
	 */
	public static function viewPath($nameBundle, $nameFolder, $viewName) {

		return __DIR__ . '/../../src/' . $nameBundle . '/views/' . $nameFolder . '/' . $viewName;

	}

	/**
	 * @param $dataClass
	 *
	 * @return array
	 */
	public static function getDataClass($dataClass) {

		$segmentsNamespace = explode('\\', $dataClass);
		$nameBundle = $segmentsNamespace[0];
		$nameFolder = str_replace('Controller', '', end($segmentsNamespace));

		return [
			'nameFolder' => $nameFolder,
			'nameBundle' => $nameBundle,
		];

	}

	/**
	 * @return \Closure
	 */
	public static function getTokenField() {

		return function () {
			$token = Service::get('security')->getToken();
			$html = '<input type="hidden" name="_token" value="' . $token . '">';
			echo $html;
		};

	}

	/**
	 * @return \Closure
	 */
	public static function include () {

		return function ($controller, $method, array $arguments = [] ) {
			Dispatcher::create($controller, $method, $arguments);
		};

	}

	/**
	 * @return \Closure
	 */
	public static function buildRoute () {

		return function ($route, array $params = []) {
			return Service::get('router')->buildRoute($route, $params);
		};

	}

	/**
	 * @param string $pathKey
	 * @param array $array
	 *
	 * @return null
	 */
	public static function arrayGet ($array, $pathKey) {

		if (!$pathKey) {
			return null;
		}

		$segments = is_array($pathKey) ? $pathKey : explode('.', $pathKey);
		$currentArray = &$array;

		foreach ($segments as $segment) {
			if (!array_key_exists($segment, $currentArray)) {
				return null;
			}

			$currentArray = $currentArray[$segment];
		}

		return $currentArray;

	}

}