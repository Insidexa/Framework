<?php

namespace Framework;
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
		$data = count($this->segments)
			? $this->segments
			: $this->segments = explode(
				'/',
				preg_replace(
					'/^\//',
					'',
					str_replace(
						$this->getBaseUrl(true),
						'',
						$this->getFullUrl()
					)
				)
			);
		var_dump($_SERVER['REQUEST_URI']);

		return $data;
	}

	public function getFullUrl() {
		return $this->getScheme() . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	}

	protected function checkPublicProperty($name, $class = NULL) {
		$class = $class ?: $this;
		$reflection = new ReflectionObject($class);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

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
		$this->method = $_SERVER['REQUEST_METHOD'];
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
		var_dump($this->getSegments());

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

		$iMax = sizeof($params);
		for ($i = 0; $i <= $iMax; $i += 2) {
			if ($params[$i])
				$this->urlParams[$params[$i]] = isset($params[$i + 1]) ?: NULL;
		}

		return $this->urlParams;
	}

	protected function getRouteFromConfig() {
		foreach ($this->config as $_next) {
			$patterExplode = explode('/', preg_replace("/^\//", '', $_next['pattern']));

			if ($patterExplode[0] === '') {
				if ($this->controllerName === $this->defaultControllerName) {
					//Site default controller
					echo 'HOME ////';
				}
			} else if ($patterExplode[0] === $this->controllerName) {
				echo $this->controllerName . "<br/>";

				if (sizeof($this->segments) === sizeof($patterExplode)) {
					$requirement = isset($_next['_requirements']) ? $_next['_requirements'] : array('_method' => 'GET');
					$pattern = '/' . str_replace('/', "\\/", $_next['pattern']) . "/i";
					if ($requirement) {

						foreach ($requirement as $_nextRule => $_valueRule) {
							$pattern = str_replace("{{$_nextRule}}", $_valueRule, $pattern);
						}

						if (isset($requirement['_method']) && (strtolower($requirement['_method']) !== strtolower($this->method))) {
							echo 'Error. Method mismatched!';
							var_dump($_next, $requirement, $this->method);
							continue;
						} else if (preg_match($pattern, '/' . implode('/', $this->segments))) {
							echo "Route found";

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
			'url' => ''
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

$config = array(
	'home'           => array(
		'pattern'    => '/',
		'controller' => 'Blog\\Controller\\PostController',
		'action'     => 'index'
	),
	'testredirect'   => array(
		'pattern'    => '/test_redirect',
		'controller' => 'Blog\\Controller\\TestController',
		'action'     => 'redirect',
	),
	'test_json'      => array(
		'pattern'    => '/test_json',
		'controller' => 'Blog\\Controller\\TestController',
		'action'     => 'getJson',
	),
	'signin'         => array(
		'pattern'    => '/signin',
		'controller' => 'Blog\\Controller\\SecurityController',
		'action'     => 'signin'
	),
	'login'          => array(
		'pattern'    => '/login',
		'controller' => 'Blog\\Controller\\SecurityController',
		'action'     => 'login'
	),
	'logout'         => array(
		'pattern'    => '/logout',
		'controller' => 'Blog\\Controller\\SecurityController',
		'action'     => 'logout'
	),
	'update_profile' => array(
		'pattern'       => '/profile',
		'controller'    => 'CMS\\Controller\\ProfileController',
		'action'        => 'update',
		'_requirements' => array(
			'_method' => 'POST'
		)
	),
	'profile'        => array(
		'pattern'    => '/profile',
		'controller' => 'CMS\\Controller\\ProfileController',
		'action'     => 'get'
	),
	'add_post'       => array(
		'pattern'    => '/posts/add',
		'controller' => 'Blog\\Controller\\PostController',
		'action'     => 'add',
		'security'   => array('ROLE_USER'),
	),
	'show_post'      => array(
		'pattern'       => '/posts/{id}',
		'controller'    => 'Blog\\Controller\\PostController',
		'action'        => 'show',
		'_requirements' => array(
			'id' => '\d+'
		)
	),
	'edit_post'      => array(
		'pattern'       => '/posts/{id}/edit',
		'controller'    => 'CMS\\Controller\\BlogController',
		'action'        => 'edit',
		'_requirements' => array(
			'id'      => '\d+',
			'_method' => 'POST'
		)

	)
);

$baseUrl = '/posts/add';

$_SERVER = array(
	'DOCUMENT_ROOT' => '/projects/Php/juton-automation-backend',
	'REMOTE_ADDR' => '::1',
	'REMOTE_PORT' => '52425',
	'SERVER_SOFTWARE' => 'PHP 5.5.27 Development Server',
	'SERVER_PROTOCOL' => 'HTTP/1.1',
	'SERVER_NAME' => 'localhost',
	'SERVER_PORT' => '7000',
	'REQUEST_METHOD' => 'GET',
	'SCRIPT_NAME' => $baseUrl . 'index.php',
	'SCRIPT_FILENAME' => '/projects/Php/juton-automation-backend' . $baseUrl . 'index.php',
	'PHP_SELF' => $baseUrl . 'index.php',
	'REQUEST_URI' => $baseUrl . '',
	'HTTP_HOST' => 'localhost:7000',
	'HTTP_CONNECTION' => 'keep-alive',
	'HTTP_CACHE_CONTROL' => 'max-age=0',
	'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
	'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
	'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36',
	'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, sdch',
	'HTTP_ACCEPT_LANGUAGE' => 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
	'HTTP_COOKIE' => 'PHPSESSID=ba705fe4e329e6c717cc48550446e22b',
	'REQUEST_TIME_FLOAT' => '1453807052.7995',
	'REQUEST_TIME' => '1453807052',
	'argv' => array(),
	'argc' => '0'
);



$_SERVER['REQUEST_URI'] = $baseUrl;
$_SERVER['REQUEST_METHOD'] = 'POST';
$router = new Router($config);

//echo $router->buildRoute('d');

echo $router;