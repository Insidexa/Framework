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

	/**
	 * Session constructor.
	 */
	public function __construct () {

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

	}

	public function destroy () {

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
			case 'string':
				$_SESSION[$name] = $value;
				break;

			case 'object':
				$_SESSION[$name] = $value();
				break;
		}



	}

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @throws \BadMethodCallException
	 */
	public static function __callStatic($name, $arguments) {

		if (method_exists(self::class, $name)) {
			self::$name();
		}

		throw new \BadMethodCallException('Method ' . $name . ' does not exists');
	}

}