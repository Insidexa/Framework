<?php

namespace Framework\Router;
use Framework\DI\Service;
use Framework\Exception\HttpNotFoundException;

/**
 * Class Router
 */
class Router {

	private $segments = array();

	private $config = array();

	private $buildUrl = '';

	protected $controllerName        = '';
	protected $actionName            = '';
	protected $method                = '';
	protected $urlParams             = '';
	protected $defaultControllerName = 'Application';
	protected $defaultActionName     = 'notFound';

	private $urlScheme;
	private $urlHost;
	private $urlPort;
	private $urlPath;
	private $_scriptUrl;
	private $_baseUrl;

	private $nameRoute = '';
	private $security = '';

	public function __construct($config) {
		$this->config = $config;

		$url = parse_url($this->getFullUrl());

		foreach ($url as $_propertyName => $propValue) {
			$this->{'url' . ucfirst($_propertyName)} = $propValue;
		}

		$this->getBaseUrl();
		$this->getSegments();

		$this->run();
	}

	public function getSegments() {
		$count = count($this->segments)
			? $this->segments
			: $this->segments = explode(
				'/',
				preg_replace(
					'/^\//',
					'',
					Service::get('request')->getUri()
				)
			);

		return count($count);
	}

	public function getFullUrl() {
		return Service::get('request')->getUrl();
	}


	protected function checkPublicProperty($name, $class = NULL) {
		$class = $class ?: $this;
		$reflection = new \ReflectionObject($class);
		$properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

		foreach ($properties as $_nextPropertyInfo) {
			if ($_nextPropertyInfo->name === $name) {
				return true;
			}
		}

		return false;
	}

	public function getScheme() {
		return $this->urlScheme ? $this->urlScheme :
			(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http');
	}

	public function getHostName() {
		return $this->urlScheme . "://" . $this->urlHost . ($this->urlPort != 80 ? ":$this->urlPort" : '');
	}

	public function getUrlPath() {
		return $this->urlPath;
	}

	public function __get($name) {
		$methodName = 'get' . ucfirst($name);
		if (method_exists($this, $methodName)) {
			return $this->{$methodName}();
		} else if (property_exists($this, $name) && $this->checkPublicProperty($name)) {
			return $this->{$name};
		}
	}

	public function __set($name, $value) {
		$methodName = 'set' . ucfirst($name);
		if (method_exists($this, $methodName)) {
			$this->{$methodName}($value);
		} else if (property_exists($this, $name) && $this->checkPublicProperty($name)) {
			$this->{$name} = $value;
		}
	}

	public function run() {
		$this->urlScheme = Service::get('request')->getScheme();
		$this->method = Service::get('request')->getMethod();
		$this->parseUrl();
	}

	public function getScriptUrl() {
		if ($this->_scriptUrl === NULL) {
			$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
			if (basename($_SERVER['SCRIPT_NAME']) === $scriptName)
				$this->_scriptUrl = $_SERVER['SCRIPT_NAME'];
			elseif (basename($_SERVER['PHP_SELF']) === $scriptName)
				$this->_scriptUrl = $_SERVER['PHP_SELF'];
			elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName)
				$this->_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
			elseif (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false)
				$this->_scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
			elseif (isset($_SERVER['DOCUMENT_ROOT']) &&
				strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0
			)
				$this->_scriptUrl =
					str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
		}

		return $this->_scriptUrl;
	}

	public function getBaseUrl($absolute = false) {
		if ($this->_baseUrl === NULL)
			$this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');

		return ($absolute ? $this->getHostName() : '') . $this->_baseUrl;
	}

	public function parseUrl() {

		switch (sizeof($this->segments)) {
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

	public function buildRoute ($routeName, array $params = []) {

		if (array_key_exists($routeName, $this->config)) {
			$this->buildUrl = $this->config[$routeName]['pattern'];

			if(!empty($params)) {
				foreach ($params as $key => $value) {
					$this->buildUrl = str_replace("{{$key}}", $value, $this->buildUrl);
				}

				$this->buildUrl = str_replace(['{', '}'], '', $this->buildUrl);

			}

		}

		return $this->buildUrl;

	}

	protected function instanceController($name, $action, array $params = []) {
		$this->controllerName = $name;
		$this->actionName = $action;
		$this->urlParams = $this->parseParams($params);
	}

	protected function parseParams($params, $notReturn = false) {

		if (!sizeof($params)) {
			return $notReturn ? [] : $this->urlParams;
		}

		foreach ($params as $key => $value) {
			$this->urlParams[$key] = $value;
		}

		return $this->urlParams;
	}

	public function getNameRoute () {

		return $this->nameRoute;

	}

	protected function getRouteFromConfig() {
		foreach ($this->config as $nameRoute => $_next) {
			$patterExplode = explode('/', preg_replace("/^\//", '', $_next['pattern']));

			if ($patterExplode[0] === '') {

				if ($this->controllerName === $this->defaultControllerName) {
					$this->nameRoute = $nameRoute;
					$this->controllerName = $_next['controller'];
					$this->actionName = $_next['action'];
					$this->security = $_next['security'] ?? '';
				}
			} else if ($patterExplode[0] === $this->controllerName) {
				//echo $this->controllerName . "<br/>";

				if (sizeof($this->segments) === sizeof($patterExplode)) {
					$requirement = isset($_next['_requirements']) ? $_next['_requirements'] : array('_method' => $this->method);
					$pattern = '/' . str_replace('/', "\\/", $_next['pattern']) . "/i";
					if ($requirement) {

						foreach ($requirement as $_nextRule => $_valueRule) {
							$pattern = str_replace("{{$_nextRule}}", $_valueRule, $pattern);
						}

						if (isset($requirement['_method']) && (strtolower($requirement['_method']) !== strtolower($this->method))) {
							continue;
						} else if (preg_match($pattern, '/' . implode('/', $this->segments))) {
							//echo "Route found";

							$this->nameRoute = $nameRoute;

							$this->controllerName = $_next['controller'];
							$this->actionName = $_next['action'];
							$this->security = $_next['security'] ?? '';

							$newSegment = array();

							foreach ($this->segments as $key => $_nextSegmentValue) {
								if (!$key) continue;
								if ($_nextSegmentValue !== $this->controllerName && $_nextSegmentValue !== $this->actionName) {
									$newSegment[] = $_nextSegmentValue;
								}
							}

							if (sizeof($newSegment)) {
								if (intval($newSegment[0]) > 0) {

									$newSegment = array_merge(array('id' => $newSegment[0]),
										$this->parseParams(array_slice($newSegment, 1), true));
								}

								$this->urlParams = $this->parseParams($newSegment);
							}
						}
					}
				}
			}
		}
	}

	public function getParam($name, $default) {
		return isset($this->urlParams[$name]) ? $this->urlParams[$name] : $this->urlParams[$name] = $default;
	}

	public function getMap () {
		return [
			'controller' => (!empty($this->controllerName)) ? $this->controllerName : $this->defaultControllerName,
			'method' => (!empty($this->actionName)) ? $this->actionName : $this->defaultActionName,
			'params' => $this->urlParams,
			'security' => $this->security
		];
	}

	public function dump () {
		var_dump($this);
	}

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

