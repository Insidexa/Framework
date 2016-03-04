<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 02.02.16
 * Time: 16:39
 */

namespace Framework\Session;

/**
 * Class Session
 *
 * @package Framework\Session
 *
 * @author Jashka
 */
class Session {

	public $returnUrl = '';

	/**
	 * Session constructor.
	 */
	public function __construct () {

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			$this->returnUrl = $_SERVER['HTTP_REFERER'];
		}

	}

	public function destroy () {

		session_unset();
		session_destroy();

	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function get ($name) {

		return array_key_exists($name, $_SESSION) ? $_SESSION[$name] : false ;

	}

	/**
	 * @param $name
	 */
	public function delete ($name) {

		if (array_key_exists($name, $_SESSION)) {
			unset($_SESSION[$name]);
		}

	}

	/**
	 * @return mixed
	 */
	public function all () {

		return $_SESSION;

	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function set ($name, $value) {

		$type = gettype($value);

		switch($type) {
			case 'boolean':
			case 'resource':
			case 'string':
			case 'object':
			case 'array':
				$_SESSION[$name] = $value;
				break;

			case 'object' && is_callable($value):
				$_SESSION[$name] = $value();
				break;
		}

	}

	/**
	 * @return array|bool
	 */
	public function getFlushMessages () {

		return ($this->get('flush') === false) ? [] : $this->get('flush');

	}

	/**
	 * @param $message
	 * @param $type
	 */
	public function addFlushMessage ($message, $type) {

		$flush = [];

		if (!empty($message)) {
			if ($type === '') {

				$flush['info'][] = $message;

			} else {

				$flush[$type][] = $message;

			}
		}

		$this->set('flush', $flush);

	}

}