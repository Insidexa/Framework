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
 *
 * @package Framework\Database
 */

/**
 * Class PDOConnector
 *
 * @package Framework\Database
 */
class PDOConnector {

	/**
	 * @var null
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
	 * @param array $config
	 *
	 * @return null
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
	 * @param $column
	 *
	 * @return $this
	 */
	protected function addColumn($column) {

		switch ($column) {
			case is_string($column):
				$this->columns = $column;
				break;

			case is_array($column):
				$this->columns = $column;
				break;
		}

		return $this;

	}

	/**
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
	 * @return $this
	 */
	protected function selectDB() {

		$this->currentSql = 'SELECT';

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function updateDB() {

		$this->currentSql = 'UPDATE';

		return $this;
	}

	/**
	 *
	 * @return $this
	 */
	protected function insertDB() {

		$this->currentSql = 'INSERT';
	}

	/**
	 * @return $this
	 */
	protected function deleteDB() {
		$this->currentSql = 'DELETE';

		return $this;
	}

	/**
	 * @param array $param
	 *
	 * @return $this
	 */
	protected function orderBy(array $param = []) {

		$this->orderBy = array_merge($this->orderBy, $param);

		return $this;
	}

	/**
	 * @param $from
	 * @param $to
	 *
	 * @return $this
	 */
	protected function limit($from, $to = null) {
		$this->limit = [
			'from' => $from,
			'to'   => $to,
		];

		return $this;
	}

	/**
	 * @return null
	 * @throws DatabaseException
	 */
	protected function assemblyQuery() {

		switch ($this->currentSql) {
			case 'SELECT':

				$this->currentSql .= $this->getStringColumns();

				$this->currentSql .= ' FROM ' . $this->_table;

				if (!empty($this->where)) {
					$this->currentSql .= $this->getStringWhere();
				}

				if (!empty($this->orderBy)) {

					$this->currentSql .= $this->getStringOrderBy();
				}

				if (!empty($this->limit)) {
					$this->currentSql .= $this->getStringLimit();
				}

				$this->queryEnd();

				break;

			case 'UPDATE':

				$this->currentSql .= ' ' . $this->_table;
				$this->currentSql .= ' SET';

				$this->currentSql .= $this->getStringUpdate();

				$this->currentSql .= $this->getStringWhere();

				$this->queryEnd();

				break;

			case 'INSERT':

				$this->currentSql .= ' INTO ';
				$this->currentSql .= $this->_table;

				$this->currentSql .= $this->insert;

				$this->queryEnd();

				break;

			case 'DELETE':

				$this->currentSql .= ' FROM ' . $this->_table;
				$this->currentSql .= ' ' . $this->getStringWhere();
				$this->queryEnd();

				break;

			default:
				throw new DatabaseException('Unknown operation: ' . $this->currentSql);
				break;
		}

		return $this->execute();

	}

	private function queryEnd() {
		$this->currentSql .= ';';
	}

	/**
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
	 * @return string
	 */
	private function getStringLimit() {

		$limitSql = ' LIMIT ' . $this->limit['from'];

		if ($this->limit['to'] !== null)
			$limitSql .= ', ' . $this->limit['to'];

		return $limitSql;

	}

	/**
	 * @param array $params
	 */
	protected function setUpdateValues(array $params = []) {

		if (!empty($params)) {
			$this->update = $params;
			$this->bindings = array_merge($params, $this->bindings);
		}

	}

	/**
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
	 * @return mixed
	 */
	private function getStringColumns() {

		$columnsSql = '';

		if (!empty($this->columns)) {

			switch ($this->columns) {
				case is_string($this->columns):
					$columnsSql .= ' ' . '*';
					break;

				case is_array($this->columns):
					$columnsSql .= ' `' . implode('`, `', $this->columns) . '`';
					break;
			}
		}

		return $columnsSql;
	}

	/**
	 * @return string
	 */
	private function getStringWhere() {

		$where = ' WHERE';
		$whereSql = '';

		foreach ($this->where as $nameColumn => $value) {
			if (!empty($nameColumn) && !empty($value)) {
				$whereSql .= ' `' . $nameColumn . '` = :' . $nameColumn . ' AND';
			}
		}

		$whereSql = substr($whereSql, 0, -4);

		return $where . $whereSql;
	}

	/**
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
	 * @return null
	 * @throws DatabaseException
	 */
	protected function execute() {
		self::$lastSql = $this->currentSql;

		try {

			$this->statement = self::$instance
				->prepare($this->currentSql);

			$this->statement->setFetchMode(\PDO::FETCH_OBJ);

			$result = $this->statement->execute($this->bindings);

			if ($this->type === 'update'
				|| $this->type === 'insert'
				|| $this->type === 'delete') {
				return $result;
			}

			$this->clear();

			return $this->statement;
		} catch (\PDOException $e) {
			throw new DatabaseException('Error execute statement: ' . $e->getMessage());
		}
	}

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
	 * @return null
	 */
	public static function getLastQuery() {
		return self::$lastSql;
	}

	/**
	 * @param $model
	 */
	protected function setModel($model) {
		$this->model = $model;
	}

	protected function closeConnection() {
		self::$instance = null;
	}

	private function __clone() {
		// TODO: Implement __clone() method.
	}

}