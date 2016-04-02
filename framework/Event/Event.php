<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 23.03.16
 * Time: 23:11
 */

namespace Framework\Event;

/**
 * Class Event
 *
 * @package Framework\Event
 */
class Event {

	/**
	 * @var array
	 */
	protected static $events = [];

	/**
	 * @param       $nameEvent
	 * @param array $arguments
	 */
	public static function emit ($nameEvent, array $arguments = []) {

		if (array_key_exists($nameEvent, self::$events)) {
			foreach (self::$events as $callback) {
				call_user_func($callback, $arguments);
			}
		}

	}

	/**
	 * @param          $eventName
	 * @param $callback
	 */
	public static function on ($eventName, $callback) {
		self::$events[$eventName] = $callback;
	}

}