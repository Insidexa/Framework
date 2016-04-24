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
	 * @var string
	 */
	public $returnUrl = '';

	/**
	 * Session constructor.
	 * Create session and change referer
	 */
	public function __construct () {

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			$this->returnUrl = $_SERVER['HTTP_REFERER'];
		}

	}

	/**
	 * Delete session
	 */
	public function destroy () {

		session_unset();
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
	 * Return all flush messages
	 *
	 * @return array|bool
	 */
	public function getFlushMessages () {

		return ($this->get('flush') === false) ? [] : $this->get('flush');

	}

	/**
	 * Add new flush message
	 *
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