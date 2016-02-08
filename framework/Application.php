<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 14:42
 */

namespace Framework;

use Framework\Database\PDOConnector;
use Framework\DI\Service;
use Framework\Exception\TokenException;
use Framework\Renderer\Render;
use Framework\Request\Request;
use Framework\Response\JsonResponse;
use Framework\Response\Response;
use Framework\Response\ResponseRedirect;
use Framework\Router\Dispatcher;
use Framework\Router\Router;
use Framework\Exception\HttpNotFoundException;
use Framework\Security\Security;
use Framework\Session\Session;

/**
 * Class Application
 *
 * @package Framework
 */
class Application
{

	/**
	 * @var string
	 */
	private $nameBundle = '';

	/**
	 * @var array
	 */
	private $config = [];

	/**
	 * Application constructor.
	 *
	 * @param $config
	 *
	 * @throws \Exception
	 */
	public function __construct($config) {

		if (is_readable($config)) {
			$this->config = include($config);

			$this->showErrors();

		} else {
			$this->appError('Config not readable');
		}

	}

	private function showErrors () {

		switch($this->config['mode']) {
			case 'dev':
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);
				break;

			case 'production':

				break;
		}

	}

	/**
	 * @param     $message
	 * @param int $code
	 *
	 * @return Response
	 */
	private function appError ($message, $code = 500) {

		$errorRender = Service::get('render')
			->render($this->config['error_500'], [
			'message' => $message,
			'code' => $code
		]);

		return new Response(Service::get('render')
			->render($this->config['main_layout'], [
			'content' => $errorRender
		]), $code);

	}

	/**
	 * @throws \Exception
	 */
	public function run () {

		try {
			$this->createServices();
			$map = Service::get('router')->getMap();

			$this->nameBundle = explode('\\', $map['controller']);

			if ($map['method'] === 'notFound') {
				$this->{$map['method']}();
			}

			if (is_array($map['security'])) {
				Service::get('security')->acl($map['security']);
			}

			if (Service::get('request')->isPost()) {

				if (Service::get('request')->post('_token') !== Service::get('security')->getToken()) {
					throw new TokenException('Token mismatch exception');
				}

			}

			$response = Dispatcher::create($map['controller'], $map['method'], $map['params']);

			$this->returnResponse($response);

		} catch (\Exception $e) {

			$this->appError($e->getMessage());

		}

	}

	/**
	 * @param $response
	 *
	 * @return Response
	 */
	private function returnResponse ($response) {

		if ($response instanceof JsonResponse) {
			return $response;
		}

		if ($response instanceof ResponseRedirect) {
			return $response;
		}

		return new Response(Service::get('render')
			->render($this->config['main_layout'], ['content' => $response]));

	}

	/**
	 * @throws Exception\DatabaseException
	 */
	private function createServices () {

		Service::set('request', new Request());
		Service::set('db', PDOConnector::getInstance($this->config['pdo']));
		Service::set('session', new Session());
		Service::set('security', new Security());
		Service::set('router', new Router($this->config['routes']));
		Service::set('render', new Render());

	}

	/**
	 * @throws HttpNotFoundException
	 */
	private function notFound () {
		$this->appError('Page not found', 404);
	}

}