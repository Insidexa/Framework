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

<<<<<<< HEAD
	/**
	 * @var string
	 */
	public $returnUrl = '';

=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	/**
	 * Session constructor.
	 * Create session and change referer
	 */
	public function __construct () {

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

	}

	/**
	 * Delete session
	 */
	public function destroy () {

		session_destroy();

	}

	/**
	 * Get value session by name key
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function get ($name) {

		return array_key_exists($name, $_SESSION) ? $_SESSION[$name] : false ;

	}

	/**
<<<<<<< HEAD
	 * Delete session data by name key
	 *
	 * @param $name
	 */
	public function delete ($name) {

		if (array_key_exists($name, $_SESSION)) {
			unset($_SESSION[$name]);
		}

	}

	/**
	 * Return all session data
	 *
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	 * @return mixed
	 */
	public function all () {

		return $_SESSION;

	}

	/**
	 * Create or change new data in session
	 *
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

<<<<<<< HEAD
	}

	/**
	 * Return all flush messages
	 *
	 * @return array|bool
	 */
	public function getFlushMessages () {
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556


	}

	/**
<<<<<<< HEAD
	 * Add new flush message
	 *
	 * @param $message
	 * @param $type
=======
	 * @param $name
	 * @param $arguments
	 *
	 * @throws \BadMethodCallException
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	 */
	public static function __callStatic($name, $arguments) {

		if (method_exists(self::class, $name)) {
			self::$name();
		}

		throw new \BadMethodCallException('Method ' . $name . ' does not exists');
	}

}