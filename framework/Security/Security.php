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
use Framework\Exception\TokenException;
use Framework\Session\Session;

/**
 * Class Security for manipulation autorize,
 * token form and permissions users
 *
 * @package Framework\Security
 */
class Security {

	/**
	 * Login url is redirect if user not autorize
	 *
	 * @var string
	 */
	private $loginUrl;

	private $model;

	/**
	 * @var Session
	 */
	private $session;

	/**
	 * Change login url, model user
	 *
	 * Security constructor.
	 *
	 * @param $url
	 *
	 * @param $model
	 *
	 */
	public function __construct($url, $model) {

		$this->loginUrl = $url;
		$this->model = $model;

		$this->session = Service::get('session');

		$this->getToken();

	}

	/**
	 * Check all user rights with the rights of the current action
	 * If the user does not have the right to act, it will be actuated Exception
	 *
	 * @param $role
	 *
	 * @throws \Exception
	 *
	 * @return ResponseRedirect
	 */
	public function acl ($role) {

		$user = $this->getUser();

		if ($user === null) {
			return new ResponseRedirect($this->loginUrl);
			//throw new AuthLoginException('Unautorized', 401);
		}

		if (!in_array($user->role, $role)) {
			throw new \Exception('Permission denied', 403);
		}

	}

	public function checkToken () {
		if (Service::get('request')->isPost()) {

			if (Service::get('request')->post('_token') !== Service::get('security')->getToken()) {
				throw new TokenException('Token mismatch exception');
			}

			$this->newToken();
		}
	}

	/**
	 * Return token if exists
	 * Otherwise create new token
	 *
	 * @return string
	 */
	public function getToken () {

		$token = $this->session->get('token');

		return (!empty($token)) ? $token : $this->newToken();

	}

	/**
	 * Generate new token
	 *
	 */
	private function newToken () {

		$this->session->set('token', md5(random_int(0, 1000)));
		return $this->session->get('token');

	}

	/**
	 * Checking user authorization
	 *
	 * @return bool
	 */
	public function isAuthenticated () {

		$flag = false;

		if ($this->session->get('user') !== false) {

			$flag = true;

		}

		return $flag;
	}

	/**
	 * Create user class
	 *
	 * @param $model
	 */
	public function setUser ($model) {

		$user = new $this->model;
		$user->id = $model->id;
		$user->email = $model->email;
		$user->role = $model->role;
		$user->password = $model->password;

		$this->session->set('user', $user);

	}

	/**
	 * Return user model
	 *
	 * @return null|object
	 */
	public function getUser () {

		$user = $this->session->get('user');

		if ($this->session->get('user') === false) {
			$user = null;
		}

		return $user;

	}

	/**
	 * Delete all data is session
	 */
	public function clear () {

		$this->session->destroy();

	}

}