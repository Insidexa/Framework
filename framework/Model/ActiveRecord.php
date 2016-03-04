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
 *
 * @package Framework\Model
 *
 * @method static bool             save()
 * @method static mixed            find( $data )
 * @method static ActiveRecord     select( $columns )
 * @method static bool             update( array $params = [] )
 * @method static bool             delete()
 * @method static ActiveRecord     where( array $param = [] )
 * @method static ActiveRecord     order( array $param = [] )
 * @method static ActiveRecord     limit( $from, $to = null )
 * @method static object|bool      get()
 *
 */
class ActiveRecord {


	/**
	 * @var array
	 */
	private static $data;

	/**
	 * Multiton
	 *
	 * Pull singleton
	 *
	 * @var array
	 */
	public static $pullActiveRecordLayout = [];

	/**
	 * Multiton
	 *
	 * Pull singleton
	 *
	 * @var array
	 */
	private static $pullModels = [];

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public static function __callStatic($name, $arguments) {
		if (!in_array(static::class, self::$pullModels)) {
			self::$pullModels[static::class] = new static;
		}
		$modelObj = self::$pullModels[static::class];

		self::getDataModel($modelObj);

		return self::callMethod($name, $arguments);
	}

	/**
	 * @param $modelObj
	 */
	private static function getDataModel ($modelObj) {

		self::$data['objVars'] = get_object_vars($modelObj);
		self::$data['pathNamespace'] = get_class($modelObj);
		self::$data['table'] = static::getTable();

	}

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public function __call($name, $arguments) {

		self::getDataModel($this);

		return self::callMethod($name, $arguments);
	}

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	private static function callMethod ($name, $arguments) {

		$modelNamespace = self::$data['pathNamespace'];

		if (!array_key_exists($modelNamespace, self::$pullActiveRecordLayout)) {
			self::$pullActiveRecordLayout[$modelNamespace] = new ActiveRecordLayout(self::$data);
		}

		$model = self::$pullActiveRecordLayout[$modelNamespace];

		if (!strstr($name, 'findBy')) {
			$reflectionMethod = new \ReflectionMethod($model, $name);

			if ($reflectionMethod->getNumberOfParameters() === 0) {
				return $model->$name();
			}
		}

		return $model->$name($arguments[0]);

	}

}