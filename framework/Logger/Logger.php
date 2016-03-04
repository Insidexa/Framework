<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 03.03.16
 * Time: 9:23
 */

namespace Framework\Logger;

/**
 * Class Logger
 *
 * @package Framework\Logger
 */
class Logger {

	private static $instance = null;

	private static $pathToLog;

	const WARN = 'warning';
	const INFO = 'info';
	const ERROR = 'error';

	protected function __construct() {

		self::$pathToLog = __DIR__ . '/../../app/logs/framework.log';

	}

	/**
	 * @return Logger|null
	 */
	public static function getInstance () {

		if (self::$instance === null) {

			self::$instance = new self;

		}

		return self::$instance;

	}

	private static function write ($msg, $type) {

		file_put_contents(self::$pathToLog, $type . ':' . $msg . "\r\n", FILE_APPEND);

	}

	public static function warn ($message) {

		self::write($message, self::WARN);

	}

	public static function info ($message) {

		self::write($message, self::INFO);

	}

	public static function error ($message) {

		self::write($message, self::ERROR);

	}

}