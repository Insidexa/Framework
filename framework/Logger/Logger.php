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
 * Write error to file
 *
 * @package Framework\Logger
 */
class Logger {

	/**
	 * @var Logger
	 */
	private static $instance;

	/**
	 * @var string
	 */
	private static $pathToLog;

	const WARN = 'warning';
	const INFO = 'info';
	const ERROR = 'error';

	/**
	 * Logger constructor.
	 * Set path to log file
	 */
	protected function __construct() {

		self::$pathToLog = __DIR__ . '/../../app/logs/framework.log';

	}

	/**
	 * Return object logger or create if not exists
	 *
	 * @return Logger|null
	 */
	public static function getInstance () {

		if (self::$instance === null) {

			self::$instance = new self;

		}

		return self::$instance;

	}

	/**
	 * Write message to file
	 *
	 * @param $msg
	 * @param $type
	 */
	private static function write ($msg, $type) {

		file_put_contents(self::$pathToLog, $type . ':' . $msg . "\r\n", FILE_APPEND);

	}

	/**
	 * @param $message
	 */
	public static function warn ($message) {

		self::write($message, self::WARN);

	}

	/**
	 * @param $message
	 */
	public static function info ($message) {

		self::write($message, self::INFO);

	}

	/**
	 *
	 *
	 * @param $message
	 */
	public static function error ($message) {

		self::write($message, self::ERROR);

	}

}