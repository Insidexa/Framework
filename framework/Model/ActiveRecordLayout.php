<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 03.02.16
 * Time: 21:17
 */

namespace Framework\Model;

use Framework\Database\Database;
use Framework\Exception\DatabaseException;

/**
 * Class ActiveRecord
 * Basic techniques for working with data as an object.
 * Layer over PDOConnector
 *
 * @package Framework\Database
 *
 * @author Jashka
 */
class ActiveRecordLayout extends Database {

	/**
	 * @var array
	 */
	private $dataModel = [];

	/**
	 * @var array
	 */
	private $data = [];

	/**
	 * @var string
	 */
	private $pathNamespace = '';

	/**
	 * @var mixed|string
	 */
	private $_model = '';

	/**
	 * @var string
	 */
	private $metaData = '';

	protected $currentTable;

	/**
	 * ActiveRecordLayout constructor.
	 *
	 * @param $data
	 */
	public function __construct($data) {
		$this->dataModel = $data['objVars'];
		$this->pathNamespace = $data['pathNamespace'];
		$pathNamespace = explode('\\', $this->pathNamespace);
		$this->_model = end($pathNamespace);
		$this->setModel($this->_model);
		$this->addTable($data['table']);
		$this->currentTable = $data['table'];
	}

	/**
	 * Need for select data the fields, as method not declared for fields
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
				$this->metaData = 'one';
				return $this->selectDB()
					->select('*')
					->where([$columnName => $arguments[0]])
					->limit(1)
					->get();
			}
			throw new DatabaseException('Not found properties ' . $columnName . ' in ' . $this->pathNamespace);
		}
	}

	/**
	 * Find column for find by field
	 * return true if find
	 *
	 * @param $columnName
	 *
	 * @return bool
	 */
	private function findColumn($columnName) {

		foreach ($this->dataModel as $key => $value) {
			if ($columnName === 'id' || $key === $columnName) return true;
//			if () return true;
		}

		return false;

	}

	/**
	 * Create new record
	 *
	 * @return null
	 * @throws DatabaseException
	 */
	public function save() {

		$this->type = 'insert';
		$params = $this->getPropertiesAndValuesChildClass();

		$this->insertValues($params);
		$this->insertDB();

		return $this->assemblyQuery();
	}

	/**
	 * Get all public fields from model
	 *
	 * @param bool $getColumns
	 *
	 * @return array
	 */
	private function getPropertiesAndValuesChildClass($getColumns = false) {

		$reflection = new \ReflectionClass($this->pathNamespace);
		$classVars = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

		$data = array_intersect_ukey($this->dataModel, $classVars, function ($key1, $key2) {
			if ($key1 === $key2)
				return 0;
		});

		if ($getColumns) {

			$columns = [];

			foreach($data as $column => $value)
				$columns[] = $column;

			return $columns;
		}

		return $data;

	}

	/**
	 * @param string $table
	 *
	 * @return $this
	 */
	public function with ($table) {

		$model = new $table;
		$table = $model::getTable();

		$tablesFrom = [];
		$columnsConditions = [];
		$where = [];

		foreach($model::$withModel as $_model) {
			$tablesFrom[] = $_model::getTable();
			$columnsConditions = array_merge($columnsConditions, $_model::$connectTo);
		}

		$columns = $this->getPropertiesAndValuesChildClass(true);
		$columns[] = $this->currentTable . '.id';
		$columns[] = $table . '.' . $model::$conditions;
		$iteratorTables = 0;

		foreach($columnsConditions as $columnsCondition) {
			$where[$table . '.' . $columnsCondition] = $tablesFrom[$iteratorTables] . '.id';
			++$iteratorTables;
		}

		$tablesFrom[] = $table;

		$this->addTable($tablesFrom)->addColumn($columns)->addWhere($where);

		return $this;

	}

	/**
	 * Return object for id or all records
	 *
	 * @param $data
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return $this|array
	 */
	public function find($data) {

		$type = gettype($data);

		switch ($type) {
			case 'integer':
				$this->metaData = 'one';
				return $this->select('*')
					->where(['id' => $data])->get();
				break;
			case 'string' && $data === 'all':
				return $this->select('*')->get();
				break;
			default:
				throw new \InvalidArgumentException('Invalid arguments');
				break;
		}

	}

	/**
	 * Update record for fields
	 *
	 * @param array $params
	 *
	 * @return array|bool|null
	 */
	public function update(array $params = []) {

		$this->type = 'update';

		$this->updateDB();

		$this->setUpdateValues($params);

		$result = $this->query();

		return $result;
	}

	/**
	 * Select all data with columns
	 *
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
	 * Set type query
	 *
	 * @return array|bool|null
	 */
	public function delete() {

		$this->type = 'delete';

		$this->deleteDB();

		return $this->query();

	}

	/**
	 * Run query and return object or bool value
	 *
	 * @return array|bool|null
	 * @throws DatabaseException
	 */
	public function query() {

		$stmp = $this->assemblyQuery();

		if (is_bool($stmp)) return $stmp;

		$this->data = $stmp->fetchAll();

		if ($this->metaData === 'one' && count($this->data) > 0) {
			return $this->data[0];
		}

		if (count($this->data) === 0) {
			return [];
		}

		return $this->data;

	}

	/**
	 * Add conditions for query
	 *
	 * @param array $param
	 *
	 * @return $this
	 */
	public function where(array $param = []) {

		$this->addWhere($param);

		return $this;

	}

	/**
	 * Add type sorting for field
	 *
	 * @param array $param
	 *
	 * @return $this
	 */
	public function order(array $param = []) {

		$this->orderBy($param);

		return $this;

	}

	/**
	 *
	 * @return array
	 */
	public function get() {

		return $this->query();

	}

}