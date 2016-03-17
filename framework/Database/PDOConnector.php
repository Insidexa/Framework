<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 25.01.16
 * Time: 15:12
 */

namespace Framework\Database;

use Framework\Exception\DatabaseException;

/**
 * Class PDOConnector
 * Constructor queries
 *
 * @package Framework\Database
 *
 * @author Jashka
 */
class PDOConnector {

	/**
	 * @var \PDO
	 */
	private static $instance;

	/**
	 * @var array
	 */
	private static $config = [];

	/**
	 * @var null
	 */
	private $currentSql = '';

	/**
	 * @var null
	 */
	private static $lastSql = '';

	/**
	 * @var null
	 */
	private $statement = '';

	/**
	 * @var array
	 */
	private $where = [];

	/**
	 * @var array
	 */
	private $limit = [];

	/**
	 * @var array|string
	 */
	private $columns;

	/**
	 * @var array
	 */
	private $orderBy = [];

	/**
	 * @var array
	 */
	private $update = [];

	/**
	 * @var array
	 */
	private $bindings = [];

	/**
	 * @var string
	 */
	private $insert = '';

	/**
	 * @var string
	 */
	private $model = '';

	/**
	 * @var string
	 */
	protected $_table = '';

	/**
	 * @var string
	 */
	protected $type = '';

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
		if (static::$instance === null) {
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

		return static::$instance;

	}

	/**
	 * Add column on array
	 *
	 * @param $column
	 *
	 * @return $this
	 */
	protected function addColumn($column) {

		$this->columns = $column;

		return $this;

	}

	/**
	 * Add conditions to array
	 *
	 * @param array $params
	 *
	 * @return $this
	 */
	protected function addWhere(array $params = []) {

		$this->where = array_merge($this->where, $params);
		$this->bindings = array_merge($this->bindings, $params);

		return $this;
	}

	/**
	 * Set type query to select
	 *
	 * @return $this
	 */
	protected function selectDB() {

		$this->currentSql = 'SELECT';

		return $this;
	}

	/**
	 * Set type query to update
	 *
	 * @return $this
	 */
	protected function updateDB() {

		$this->currentSql = 'UPDATE';

		return $this;
	}

	/**
	 * Set type query to insert record
	 *
	 * @return $this
	 */
	protected function insertDB() {

		$this->currentSql = 'INSERT';
	}

	/**
	 * Set type query to delete record
	 *
	 * @return $this
	 */
	protected function deleteDB() {
		$this->currentSql = 'DELETE';

		return $this;
	}

	/**
	 * Add order conditions to array
	 *
	 * @param array $param
	 *
	 * @return $this
	 */
	protected function orderBy(array $param = []) {

		$this->orderBy = array_merge($this->orderBy, $param);

		return $this;
	}

	/**
	 * Change the limit of delivery of results
	 *
	 * @param $from
	 * @param $to
	 *
	 * @return $this
	 */
	public function limit($from, $to = null) {
		$this->limit = [
			'from' => $from,
			'to'   => $to,
		];

		return $this;
	}

	/**
	 * Collects a complete request by type
	 *
	 * @return null
	 * @throws DatabaseException
	 */
	protected function assemblyQuery() {

		switch ($this->currentSql) {
			case 'SELECT':

				$this->currentSql .= $this->getStringColumns();
				$this->currentSql .= ' FROM ' . $this->_table;

				if (count($this->where) !== 0) {
					$this->currentSql .= $this->getStringWhere();
				}

				if (count($this->orderBy) !== 0) {
					$this->currentSql .= $this->getStringOrderBy();
				}

				if (count($this->limit) !== 0) {
					$this->currentSql .= $this->getStringLimit();
				}

				break;

			case 'UPDATE':

				$this->currentSql .= ' ' . $this->_table;
				$this->currentSql .= ' SET';
				$this->currentSql .= $this->getStringUpdate();
				$this->currentSql .= $this->getStringWhere();

				break;

			case 'INSERT':

				$this->currentSql .= ' INTO ';
				$this->currentSql .= $this->_table;
				$this->currentSql .= $this->insert;

				break;

			case 'DELETE':

				$this->currentSql .= ' FROM ' . $this->_table;
				$this->currentSql .= ' ' . $this->getStringWhere();

				break;

			default:
				throw new DatabaseException('Unknown operation: ' . $this->currentSql);
				break;
		}

		$this->queryEnd();

		return $this->execute();

	}

	/**
	 * Ends string query
	 */
	private function queryEnd() {
		$this->currentSql .= ';';
	}

	/**
	 * Parse insert arguments to string query
	 *
	 * @param array $params
	 */
	protected function insertValues(array $params = []) {

		// TODO: suppose that id AI
		unset($params['id']);

		$this->bindings = $params;
		$columns = ' (';
		$values = ' (';

		foreach ($params as $columnName => $value) {
			$columns .= '`' . $columnName . '`, ';
			$values .= ':' . $columnName . ', ';
		}

		$columns = substr($columns, 0, -2) . ')';
		$values = ' VALUES ' . substr($values, 0, -2) . ')';

		$this->insert .= $columns . $values;

	}

	/**
	 * Parse limit data to string
	 *
	 * @return string
	 */
	private function getStringLimit() {

		$limitSql = ' LIMIT ' . $this->limit['from'];

		if ($this->limit['to'] !== null)
			$limitSql .= ', ' . $this->limit['to'];

		return $limitSql;

	}

	/**
	 * Set update data
	 *
	 * @param array $params
	 */
	protected function setUpdateValues(array $params = []) {

		if (count($params) !== 0) {
			$this->update = $params;
			$this->bindings = array_merge($params, $this->bindings);
		}

	}

	/**
	 * Parse update data to string query
	 *
	 * @return string
	 */
	private function getStringUpdate() {

		$updateSql = '';

		foreach ($this->update as $nameColumn => $value) {
			if (!empty($nameColumn) && !empty($value)) {
				$updateSql .= ' `' . $nameColumn . '` = :' . $nameColumn . ', ';
			}
		}

		$updateSql = substr($updateSql, 0, -2);

		return $updateSql;

	}

	/**
	 * Parse columns to string query
	 *
	 * @return mixed
	 */
	private function getStringColumns() {

		$columnsSql = '';

		switch ($this->columns) {
			case is_string($this->columns):
				$columnsSql = ' ' . $this->columns;
				break;

			case is_array($this->columns):
				$columnsSql = ' `' . implode('`, `', $this->columns) . '`';
				break;
		}

		return $columnsSql;
	}

	/**
	 * Parse where conditions to string query
	 *
	 * @return string
	 */
	private function getStringWhere() {

		$where = ' WHERE';
		$whereSql = '';

		foreach ($this->where as $nameColumn => $value) {
//			if (!empty($nameColumn) && !empty($value)) {
				$whereSql .= ' `' . $nameColumn . '` = :' . $nameColumn . ' AND';
//			}
		}

		$whereSql = substr($whereSql, 0, -4);

		return $where . $whereSql;
	}

	/**
	 * Parse order conditions to string query
	 *
	 * @return string
	 */
	private function getStringOrderBy() {
		$orderBy = ' ORDER BY ';
		$orderBySql = '';

		foreach ($this->orderBy as $nameColumn => $value) {
			if (!empty($nameColumn) && !empty($value)) {
				$orderBySql .= '`' . $nameColumn . '` ' . $value . ', ';
			}
		}

		$orderBySql = substr($orderBySql, 0, -2);

		return $orderBy . $orderBySql;
	}

	/**
	 * Run query
	 * clear data after query
	 *
	 * @return object|integer
	 * @throws DatabaseException
	 */
	protected function execute() {
		self::$lastSql = $this->currentSql;

		try {

			$this->statement = self::$instance
				->prepare($this->currentSql);

			$this->statement->setFetchMode(\PDO::FETCH_OBJ);

			$result = $this->statement->execute($this->bindings);

			$this->clear();

			if ($this->type === 'update'
				|| $this->type === 'insert'
				|| $this->type === 'delete') {

				$this->type = '';

				return $result;
			}

			return $this->statement;
		} catch (\PDOException $e) {
			throw new DatabaseException('Error execute statement: ' . $e->getMessage());
		}
	}

	/**
	 * Cleared data for query
	 */
	private function clear() {

		$this->where = [];
		$this->limit = [];
		$this->columns = [];
		$this->orderBy = [];
		$this->update = [];
		$this->bindings = [];
		$this->insert = '';

	}

	/**
	 * Return string last query
	 *
	 * @return null
	 */
	public static function getLastQuery() {
		return self::$lastSql;
	}

	/**
	 * Set model
	 *
	 * @param $model
	 */
	protected function setModel($model) {
		$this->model = $model;
	}

	/**
	 * Close connection
	 */
	protected function closeConnection() {
		self::$instance = null;
	}

	private function __clone() {
		// TODO: Implement __clone() method.
	}

}