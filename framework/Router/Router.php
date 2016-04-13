<?php

namespace Framework\Router;

use Framework\DI\Service;
<<<<<<< HEAD
use Framework\Request\Request;

/**
 * Class Router
 * Manipulation url
 *
 * @package Framework\Router
=======
use Framework\Exception\HttpNotFoundException;

/**
 * Class Router
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
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

<<<<<<< HEAD
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
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
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

<<<<<<< HEAD
	/**
	 * Change segments and return count if segments
	 *
	 * @return int
	 */
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
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

<<<<<<< HEAD
	/**
	 * Run parsing url and change scheme, method
	 */
=======
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

>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	public function run() {
		$this->urlScheme = $this->request->getScheme();
		$this->method = $this->request->getMethod();
		$this->parseUrl();
	}

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

	public function getBaseUrl($absolute = false) {
		if ($this->_baseUrl === NULL)
			$this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');

		return ($absolute ? $this->request->getUrl() : '') . $this->_baseUrl;
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

<<<<<<< HEAD
	/**
	 * Create url for name route with arguments
	 *
	 * @param       $routeName
	 * @param array $params
	 *
	 * @return mixed|string
	 */
	public function buildRoute($routeName, array $params = []) {
=======
	public function buildRoute ($routeName, array $params = []) {
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556

		if (array_key_exists($routeName, $this->config)) {
			$this->buildUrl = $this->config[ $routeName ]['pattern'];

<<<<<<< HEAD
			if (count($params) > 0) {
=======
			if(!empty($params)) {
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
				foreach ($params as $key => $value) {
					$this->buildUrl = str_replace("{{$key}}", $value, $this->buildUrl);
				}

				$this->buildUrl = str_replace(['{', '}'], '', $this->buildUrl);

			}

		}

		return $this->buildUrl;

	}

<<<<<<< HEAD
	/**
	 * Set controller, method, arguments method
	 *
	 * @param       $name
	 * @param       $action
	 * @param array $params
	 */
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	protected function instanceController($name, $action, array $params = []) {
		$this->controllerName = $name;
		$this->actionName = $action;
		$this->urlParams = $this->parseParams($params);
	}

<<<<<<< HEAD
	/**
	 * Set url params
	 *
	 * @param      $params
	 * @param bool $notReturn
	 *
	 * @return array|string
	 */
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	protected function parseParams($params, $notReturn = false) {

		if (!sizeof($params)) {
			return $notReturn ? [] : $this->urlParams;
		}

<<<<<<< HEAD
		if ($countParams === 1) {
			return $params;
		}

		for ($i = 0; $i < $countParams; $i++) {
			$key = $params[ $i++ ];
			$this->urlParams[ $key ] = $params[ $i ];
=======
		foreach ($params as $key => $value) {
			$this->urlParams[$key] = $value;
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
		}

		return $this->urlParams;
	}

<<<<<<< HEAD
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
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	protected function getRouteFromConfig() {
		foreach ($this->config as $_next) {
			$patterExplode = explode('/', preg_replace("/^\//", '', $_next['pattern']));

			if ($patterExplode[0] === '') {
				if ($this->controllerName === $this->defaultControllerName) {
					$this->controllerName = $_next['controller'];
					$this->actionName = $_next['action'];
				}
<<<<<<< HEAD
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
=======
			} else if ($patterExplode[0] === $this->controllerName) {
				//echo $this->controllerName . "<br/>";

				if (sizeof($this->segments) === sizeof($patterExplode)) {
					$requirement = isset($_next['_requirements']) ? $_next['_requirements'] : array('_method' => 'GET');
					$pattern = '/' . str_replace('/', "\\/", $_next['pattern']) . "/i";
					if ($requirement) {

						foreach ($requirement as $_nextRule => $_valueRule) {
							$pattern = str_replace("{{$_nextRule}}", $_valueRule, $pattern);
						}

						if (isset($requirement['_method']) && (strtolower($requirement['_method']) !== strtolower($this->method))) {
							throw new HttpNotFoundException('Method mismatched');
							/*echo 'Error. Method mismatched!';
							var_dump($_next, $requirement, $this->method);
							continue;*/
						} else if (preg_match($pattern, '/' . implode('/', $this->segments))) {
							//echo "Route found";

							$this->controllerName = $_next['controller'];
							$this->actionName = $_next['action'];

							$newSegment = array();

							foreach ($this->segments as $key => $_nextSegmentValue) {
								if (!$key) continue;
								if ($_nextSegmentValue !== $this->controllerName && $_nextSegmentValue !== $this->actionName) {
									$newSegment[] = $_nextSegmentValue;
								}
							}

							if (sizeof($newSegment)) {
								if (intval($newSegment[0]) > 0) {
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556

									$newSegment = array_merge(array('id' => $newSegment[0]),
										$this->parseParams(array_slice($newSegment, 1), true));
								}

<<<<<<< HEAD
		if (count($newSegment)) {
			if ((int)$newSegment[0] > 0) {
				$newSegment = array_merge(['id' => $newSegment[0]],
					$this->parseParams(array_slice($newSegment, 1), true));
=======
								$this->urlParams = $this->parseParams($newSegment);
							}
						}
					}
				}
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
			}
		}
	}

<<<<<<< HEAD
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
=======
	public function getParam($name, $default) {
		return isset($this->urlParams[$name]) ? $this->urlParams[$name] : $this->urlParams[$name] = $default;
	}

	public function getMap () {
		return [
			'controller' => (!empty($this->controllerName)) ? $this->controllerName : $this->defaultControllerName,
			'method' => (!empty($this->actionName)) ? $this->actionName : $this->defaultActionName,
			'params' => $this->urlParams
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
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

