<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 23:51
 */

namespace Framework\Model;

/**
 * Class ActiveRecord
 */
class ActiveRecord {


	private static $data;

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public static function __callStatic($name, $arguments) {
		self::$data['objVars'] = get_object_vars(new static);
		self::$data['pathNamespace'] = get_class(new static);
		self::$data['table'] = static::getTable();

		$model = new ActiveRecordLayout(self::$data);
		return $model->$name($arguments[0]);
	}

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public function __call($name, $arguments) {

		self::$data['objVars'] = get_object_vars($this);
		self::$data['pathNamespace'] = get_class($this);
		self::$data['table'] = static::getTable();

		$model = new ActiveRecordLayout(self::$data);

		return $model->$name($arguments[0]);
	}

}