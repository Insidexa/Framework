<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 04.02.16
 * Time: 12:04
 */

namespace Framework\Security;
use Framework\DI\Service;
use Framework\Exception\AuthLoginException;
use Framework\Response\ResponseRedirect;

/**
 * Class Security
 *
 * @package Framework\Security
 */
class Security {

	private $loginUrl;

	/**
	 * Security constructor.
	 *
	 * @param $url
	 */
	public function __construct($url) {

		$this->loginUrl = $url;

		$this->getToken();

	}

	/**
	 * @param $role
	 *
	 * @throws \Exception
	 *
	 * @return ResponseRedirect
	 */
	public function acl ($role) {

		$user = $this->getUser();

		if ($user === null) {
			return new ResponseRedirect($this->loginUrl, 301);
			//throw new AuthLoginException('Unautorized', 401);
		}

		if (!in_array($user->role, $role)) {
			throw new \Exception('Permission denied', 403);
		}

	}

	/**
	 * @return mixed
	 */
	public function getToken () {

		$token = Service::get('session')->get('token');

		return (!empty($token)) ? $token : $this->newToken();

	}

	/**
	 * @return mixed
	 */
	private function newToken () {

		return Service::get('session')->set('token', md5(random_int(0, 1000)));

	}

	/**
	 * @return bool
	 */
	public function isAuthenticated () {

		$flag = false;

		if (Service::get('session')->get('user') !== false) {

			$flag = true;

		}

		return $flag;
	}

	/**
	 * @param $model
	 */
	public function setUser ($model) {

		Service::get('session')->set('user', $model);

	}

	/**
	 * @return mixed
	 */
	public function getUser () {

		$user = Service::get('session')->get('user');

		if (Service::get('session')->get('user') === false) {
			$user = null;
		}

		return $user;

	}


	public function clear () {

		Service::get('session')->destroy();

	}

}