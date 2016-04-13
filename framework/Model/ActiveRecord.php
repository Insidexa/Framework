<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 23:51
 */

namespace Framework\Model;

use Framework\Database\PDOConnector;
use Framework\Exception\DatabaseException;

/**
 * Class ActiveRecord
 * Record as an object management
 *
<<<<<<< HEAD
 * @package Framework\Model
 *
 * @method bool                    save()
 * @method static object           find( $data )
 * @method static bool             update( array $params = [] )
 * @method static bool             delete()
 * @method static ActiveRecord     where( array $param = [] )
 * @method static ActiveRecord     order( array $param = [] )
 * @method static ActiveRecord     limit( $from, $to = null )
 * @method static object|bool      get()
 *
=======
 * @package Framework\Database
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
 */
class ActiveRecord extends PDOConnector {

	/**
	 * @var array
	 */
	private $dataModel = [];

	/**
	 * @var array
	 */
	private $data = [];

	private $pathNamespace = '';

	/**
	 * @var mixed|string
	 */
	private $_model = '';

	/**
	 * ActiveRecord constructor.
	 */
	public function __construct() {
		$this->dataModel = get_object_vars($this);
		$this->pathNamespace = get_class($this);
		$pathNamespace = explode('\\', $this->pathNamespace);
		$this->_model = end($pathNamespace);
		$this->setModel($this->_model);
		$this->_table = static::getTable();
	}

	/**
	 * We need to call the static methods
	 * I like how it is done in laravel
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return array
	 * @throws DatabaseException
	 */
	public function __call($name, $arguments) {
		if (strstr($name, 'findBy', false)) {
			$columnName = strtolower(str_replace('findBy', '', $name));
			if ($this->findColumn($columnName)) {
				return $this->selectDB()
					->select('*')
					->where([$columnName => $arguments[0]])
					->limit(1)
					->get();
			}
			throw new DatabaseException('Not found properties in ' . $this->pathNamespace);
		}
	}

	/**
	 * @param $columnName
	 *
	 * @return bool
	 */
	private function findColumn($columnName) {

		foreach ($this->dataModel as $key => $value) {
			if ($key === $columnName) return true;
		}

		return false;

	}

	/**
	 * @return null
	 */
	public function save() {

		$this->type = 'insert';
		$params = $this->getPropertiesAndValuesChildClass();

		$this->insertValues($params);
		$this->insertDB();

		return $this->assemblyQuery();
	}

	/**
<<<<<<< HEAD
	 * The same thing, only it is necessary to create an object
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
=======
	 * @return array
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	 */
	private function getPropertiesAndValuesChildClass() {

		$objectVars = get_object_vars($this);

		$reflection = new \ReflectionClass($this->pathNamespace);
		$classVars = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

		$vars = array_intersect_ukey($objectVars, $classVars, function ($key1, $key2) {
			if ($key1 === $key2)
				return 0;
		});

		return $vars;

	}

	/**
<<<<<<< HEAD
	 * Call method in ActiveRecordLayout without or with arguments
	 *
	 * @param $name
	 * @param $arguments
=======
	 * @param $data
	 *
	 * @throws \InvalidArgumentException
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	 *
	 * @return $this|array
	 */
	public function find($data) {

		switch ($data) {
			case is_int($data):
				return $this->selectDB()
					->where(['id' => $data])->get();

				break;
			case is_string($data) && $data === 'all':
				return $this->selectDB()
					->select('*')->get();
				break;

			default:
				throw new \InvalidArgumentException('Invalid arguments');
				break;
		}

		return $this;
	}

	/**
	 * @param array $params
	 *
	 * @return array|bool
	 */
	public function update(array $params = []) {

		$this->type = 'update';

		$this->updateDB();

		$this->setUpdateValues($params);

		$result = $this->query();

		return $result;
	}

	/**
	 * @param array | string $columns
	 *
	 * @return $this
	 */
	public function select($columns) {

		$this->selectDB();
		$this->addColumn($columns);

		return $this;
	}

	/**
	 *
	 * @return $this
	 */
	public function delete() {

		$this->type = 'delete';

		$this->deleteDB();

		return $this->query();

	}

	/**
	 * @return array|bool
	 */
	public function query() {

		$stmp = $this->assemblyQuery();

		if (is_bool($stmp)) return $stmp;

		while ($object = $stmp->fetch()) {
			$this->data[] = $object;
		}

		if (count($this->data) === 1) {
			$this->data = $this->data[0];
		}

		if (count($this->data) === 0) {
			return false;
		}

		return $this->data;

	}

	/**
	 * @param array $param
	 *
	 * @return $this
	 */
	public function where(array $param = []) {

		$this->addWhere($param);

		return $this;

	}

	/**
	 * @param array $param
	 *
	 * @return $this
	 */
	public function order(array $param = []) {

		$this->orderBy($param);

		return $this;

	}

	/**
	 * @param      $from
	 * @param null $to
	 *
	 * @return $this
	 */
	public function limit($from, $to = null) {

		parent::limit($from, $to);

		return $this;
	}

	/**
	 * @return array
	 */
	public function get() {

		return $this->query();

	}

}