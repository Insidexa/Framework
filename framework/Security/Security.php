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

/**
 * Class Security
 *
 * @package Framework\Security
 */
class Security {

	/**
	 * Security constructor.
	 */
	public function __construct() {

		$this->getToken();

	}

	/**
	 * @param $role
	 *
	 * @throws \Exception
	 */
	public function acl ($role) {

		$user = $this->getUser();

		if ($user === null) {
			throw new \Exception('Permission denied: Unautorized');
		}

		if (!in_array($user->role, $role)) {
			throw new \Exception('Permission denied');
		}

	}

	/**
	 * @return mixed
	 */
	public function getToken () {

		$token = Service::get('session')->get('token');

		if (!empty($token)) return $token;

		return $this->newToken();

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