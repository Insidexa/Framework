<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 30.03.16
 * Time: 9:29
 */

namespace Framework\Database;

use Framework\Exception\DatabaseException;

/**
 * Class DBConnection
 *
 * @package Framework\Database
 */
abstract class DBConnection {

	/**
	 * @var \PDO
	 */
	protected static $instance;

	/**
	 * @var array
	 */
	protected static $config;

	/**
	 * Return the same object
	 * Singleton
	 *
	 * @param array $config
	 *
	 * @return \PDO
	 * @throws DatabaseException
	 */
	public static function getInstance(array $config = []) {
		if (self::$instance === null) {
			self::$config = $config;
			try {
				static::$instance = new \PDO(
					self::$config['dns'],
					self::$config['user'],
					self::$config['password']
				);

				static::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

			} catch (\PDOException $e) {
				throw new DatabaseException($e->getMessage());
			}

		}

		return self::$instance;

	}

}