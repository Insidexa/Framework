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
	 * Return full path to view  with name bundle, name folder and view name
	 *
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
	 * Return name folder for view and name bundle
	 *
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
	 * Return closure which creates input with token for POST request
	 *
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
	 * Return closure which call Dispatcher and insert response in template
	 *
	 * @return \Closure
	 */
	public static function include () {

		return function ($controller, $method, array $arguments = [] ) {
			Dispatcher::create($controller, $method, $arguments);
		};

	}

	/**
	 * Return closure which generate url from config with or without arguments
	 *
	 * @return \Closure
	 */
	public static function buildRoute () {

		return function ($route, array $params = []) {
			return Service::get('router')->buildRoute($route, $params);
		};

	}

	/**
	 * Return value from array
	 * Search path keys in array
	 * Like Laravel
	 *
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