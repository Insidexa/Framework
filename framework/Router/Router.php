<?php

namespace Framework\Router;

use Framework\DI\Service;
use Framework\Request\Request;

/**
 * Class Router
 * Manipulation url
 *
 * @package Framework\Router
 */
class Router {

	private $segments = [];

	private $config = [];

	private $buildUrl = '';

	protected $controllerName = '';
	protected $actionName = '';
	protected $method = '';
	protected $urlParams = '';
	protected $defaultControllerName = 'Application';
	protected $defaultActionName = 'notFound';

	private $urlScheme;
	private $urlHost;
	private $urlPort;
	private $urlPath;
	private $_scriptUrl;
	private $_baseUrl;

	private $nameRoute = '';
	private $security = '';

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * Router constructor.
	 * Change config
	 * Run life cycle router
	 * Parse url on segments
	 *
	 * @param $config
	 */
	public function __construct($config) {
		$this->config = $config;

		$this->request = Service::get('request');

		$url = parse_url($this->request->getUrl());

		foreach ($url as $_propertyName => $propValue) {
			$this->{'url' . ucfirst($_propertyName)} = $propValue;
		}

		$this->getBaseUrl();
		$this->getSegments();

		$this->run();
	}

	/**
	 * Change segments and return count if segments
	 *
	 * @return int
	 */
	public function getSegments() {
		$count = count($this->segments)
			? $this->segments
			: $this->segments = explode(
				'/',
				preg_replace(
					'/^\//',
					'',
					$this->request->getStringUri()
				)
			);

		return count($count);
	}

	/**
	 * Run parsing url and change scheme, method
	 */
	public function run() {
		$this->urlScheme = $this->request->getScheme();
		$this->method = $this->request->getMethod();
		$this->parseUrl();
	}

	/**
	 * @return mixed|null|string
	 */
	public function getScriptUrl() {
		if ($this->_scriptUrl === NULL) {
			$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
			if (basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
				$this->_scriptUrl = $_SERVER['SCRIPT_NAME'];
			} elseif (basename($_SERVER['PHP_SELF']) === $scriptName) {
				$this->_scriptUrl = $_SERVER['PHP_SELF'];
			} elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
				$this->_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
			} elseif (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
				$this->_scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
			}
			elseif (isset($_SERVER['DOCUMENT_ROOT']) &&
				strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0
			) {
				$this->_scriptUrl =
					str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
			}
		}

		return $this->_scriptUrl;
	}

	/**
	 * @param bool $absolute
	 *
	 * @return string
	 */
	public function getBaseUrl($absolute = false) {
		if ($this->_baseUrl === NULL)
			$this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');

		return ($absolute ? $this->request->getUrl() : '') . $this->_baseUrl;
	}

	/**
	 *
	 */
	public function parseUrl() {

		switch (count($this->segments)) {
			case 1:
				if ($this->segments[0] === '') {
					$this->instanceController($this->defaultControllerName, $this->defaultActionName);
				} else {
					$this->instanceController($this->segments[0], $this->defaultActionName);
				}
				break;
			default:
				$this->controllerName = $this->segments[0];
				break;
		}

		$this->getRouteFromConfig();
	}

	/**
	 * Create url for name route with arguments
	 *
	 * @param       $routeName
	 * @param array $params
	 *
	 * @return mixed|string
	 */
	public function buildRoute($routeName, array $params = []) {

		if (array_key_exists($routeName, $this->config)) {
			$this->buildUrl = $this->config[ $routeName ]['pattern'];

			if (count($params) > 0) {
				foreach ($params as $key => $value) {
					$this->buildUrl = str_replace("{{$key}}", $value, $this->buildUrl);
				}

				$this->buildUrl = str_replace(['{', '}'], '', $this->buildUrl);

			}

		}

		return $this->buildUrl;

	}

	/**
	 * Set controller, method, arguments method
	 *
	 * @param       $name
	 * @param       $action
	 * @param array $params
	 */
	protected function instanceController($name, $action, array $params = []) {
		$this->controllerName = $name;
		$this->actionName = $action;
		$this->urlParams = $this->parseParams($params);
	}

	/**
	 * Set url params
	 *
	 * @param      $params
	 * @param bool $notReturn
	 *
	 * @return array|string
	 */
	protected function parseParams($params, $notReturn = false) {

		$countParams = count($params);

		if (!$countParams) {
			return $notReturn ? [] : $this->urlParams;
		}

		if ($countParams === 1) {
			return $params;
		}

		for ($i = 0; $i < $countParams; $i++) {
			$key = $params[ $i++ ];
			$this->urlParams[ $key ] = $params[ $i ];
		}

		return $this->urlParams;
	}

	/**
	 * Return current name route
	 *
	 * @return string
	 */
	public function getNameRoute() {

		return $this->nameRoute;

	}

	/**
	 * Compares parsed segments from config routes
	 */
	protected function getRouteFromConfig() {
		foreach ($this->config as $nameRoute => $_next) {
			$patterExplode = explode('/', preg_replace("/^\//", '', $_next['pattern']));

			if ($patterExplode[0] === '') {

				if ($this->controllerName === $this->defaultControllerName) {
					$this->setMap($nameRoute, $_next);
				}
			} else if ($patterExplode[0] === $this->controllerName && count($this->segments) === count($patterExplode)) {

				$requirement = array_key_exists('_requirements', $_next) ? $_next['_requirements'] : ['_method' => $this->method];
				$pattern = '/' . str_replace('/', '\\/', $_next['pattern']) . '/i';
				// isset requirements
				if ($requirement) {

					foreach ($requirement as $_nextRule => $_valueRule) {
						$pattern = str_replace('{' . $_nextRule . '}', $_valueRule, $pattern);
					}

					if (array_key_exists('_method', $requirement) && (strtolower($requirement['_method']) !== strtolower($this->method))) {
						continue;
					} else if (preg_match($pattern, '/' . implode('/', $this->segments))) {

						$this->parseRequirements($nameRoute, $_next);

					}
				}
			}
		}
	}

	/**
	 * Change to the appropriate controller card for this route
	 *
	 * @param $nameRoute
	 * @param $_next
	 */
	private function setMap($nameRoute, $_next) {
		$this->nameRoute = $nameRoute;
		$this->controllerName = $_next['controller'];
		$this->actionName = $_next['action'];
		$this->security = $_next['security'] ?? '';
	}

	/**
	 * @param $nameRoute
	 * @param $_next
	 */
	private function parseRequirements($nameRoute, $_next) {
		$this->setMap($nameRoute, $_next);

		$newSegment = [];

		foreach ($this->segments as $key => $_nextSegmentValue) {
			if (!$key) {
				continue;
			}
			if ($_nextSegmentValue !== $this->controllerName && $_nextSegmentValue !== $this->actionName) {
				$newSegment[] = $_nextSegmentValue;
			}
		}

		if (count($newSegment)) {
			if ((int)$newSegment[0] > 0) {
				$newSegment = array_merge(['id' => $newSegment[0]],
					$this->parseParams(array_slice($newSegment, 1), true));
			}

			$this->urlParams = $this->parseParams($newSegment);
		}
	}

	/**
	 * Return param for name key
	 *
	 * @param $name
	 * @param $default
	 *
	 * @return mixed
	 */
	public function getParam($name, $default) {
		if (array_key_exists($name, $this->urlParams)) {
			return $this->urlParams[ $name ];
		}
		return $this->urlParams[ $name ] = $default;
	}

	/**
	 * Return array with name controller, method, arguments and permissions for security
	 *
	 * @return array
	 */
	public function getMap() {
		return [
			'controller' => (!empty($this->controllerName)) ? $this->controllerName : $this->defaultControllerName,
			'method'     => (!empty($this->actionName)) ? $this->actionName : $this->defaultActionName,
			'params'     => $this->urlParams,
			'security'   => $this->security,
		];
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return PHP_EOL . "actionName: \t\t" . $this->actionName . PHP_EOL
		. "controllerName: \t" . $this->controllerName . PHP_EOL
		. "method: \t\t" . $this->method . PHP_EOL
		. "urlParams: \t\t" . $this->urlParams . PHP_EOL
		. "baseUrl: \t\t" . $this->_baseUrl . PHP_EOL
		. "scriptUrl: \t\t" . $this->_scriptUrl . PHP_EOL
		. "buildUrl: \t\t" . $this->buildUrl . PHP_EOL
		. "segments\t\t" . implode(', ', $this->segments) . PHP_EOL
		. "urlHost: \t\t" . $this->urlHost . PHP_EOL
		. "urlPath: \t\t" . $this->urlPath . PHP_EOL
		. "urlPort: \t\t" . $this->urlPort . PHP_EOL
		. "urlScheme: \t\t" . $this->urlScheme . PHP_EOL;
	}

}

