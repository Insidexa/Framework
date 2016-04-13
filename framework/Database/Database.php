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
 */

/**
 * Class PDOConnector
 *
 * @package Framework\Database
 */
abstract class Database {

	/**
	 * @var \PDO
	 */
	private static $instance;

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
	 * @var array
	 */
	protected $_tables = [];

	/**
	 * @var string
	 */
	protected $type = '';

	/**
	 * Set db connection
	 *
	 * @param \PDO $connection
	 *
<<<<<<< HEAD:framework/Database/Database.php
=======
	 * @return null
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556:framework/Database/PDOConnector.php
	 * @throws DatabaseException
	 */
	public static function setConnection(\PDO $connection) {

		self::$instance = $connection;

	}

	/**
	 * @param string|array $tables
	 *
	 * @return $this
	 * @throws DatabaseException
	 */
	protected function addTable ($tables) {

		$type = gettype($tables);

		switch ($type) {
			case 'array':
				$this->_tables = array_merge($this->_tables, $tables);
				break;

			case 'string':
				$this->_tables[] = $tables;
				break;

			default:
				throw new DatabaseException('Cannot add tables');
				break;
		}

		$this->_tables = array_unique($this->_tables);

		return $this;

	}

	/**
	 * Add column on array
	 *
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
	protected function limit($from, $to = null) {
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
<<<<<<< HEAD:framework/Database/Database.php
				$this->currentSql .= ' FROM ' . $this->getStringTables();
=======

				$this->currentSql .= ' FROM ' . $this->_table;
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556:framework/Database/PDOConnector.php

				if (count($this->where) !== 0) {
					$this->currentSql .= $this->getStringWhere();
				}

<<<<<<< HEAD:framework/Database/Database.php
				if (count($this->orderBy) !== 0) {
=======
				if (!empty($this->orderBy)) {

>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556:framework/Database/PDOConnector.php
					$this->currentSql .= $this->getStringOrderBy();
				}

				if (count($this->limit) !== 0) {
					$this->currentSql .= $this->getStringLimit();
				}

				$this->queryEnd();

				break;

			case 'UPDATE':

				$this->currentSql .= ' ' . $this->getStringTables();
				$this->currentSql .= ' SET';

				$this->currentSql .= $this->getStringUpdate();

				$this->currentSql .= $this->getStringWhere();

				$this->queryEnd();

				break;

			case 'INSERT':

				$this->currentSql .= ' INTO ';
<<<<<<< HEAD:framework/Database/Database.php
				$this->currentSql .= $this->getStringTables();
=======
				$this->currentSql .= $this->_table;

>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556:framework/Database/PDOConnector.php
				$this->currentSql .= $this->insert;

				$this->queryEnd();

				break;

			case 'DELETE':

				$this->currentSql .= ' FROM ' . $this->getStringTables();
				$this->currentSql .= ' ' . $this->getStringWhere();
				$this->queryEnd();

				break;

			default:
				throw new DatabaseException('Unknown operation: ' . $this->currentSql);
				break;
		}

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
	 * @return string
	 * @throws DatabaseException
	 */
	protected function getStringTables () {

		$tablesSql = '';

		if (count($this->_tables) === 1) {
			$tablesSql = $this->_tables[0] ;
		}

		if (count($this->_tables) > 1) {

			foreach($this->_tables as $table) {

				$tablesSql .= '`' . $table . '`, ';

			}

			$tablesSql = substr($tablesSql, 0, -2);

		}

		if (empty($tablesSql)) throw new DatabaseException('Specify the table/s');

		return $tablesSql;

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
				foreach($this->columns as $column) {

					if (strstr($column, '.', false)) {
						$data = explode('.', $column);
						$columnsSql .= "`$data[0]`.`$data[1]`, ";
					} else {
						$columnsSql .= '`' . $column . '`, ';
					}

				}

				$columnsSql = substr($columnsSql, 0, -2);
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
<<<<<<< HEAD:framework/Database/Database.php
			if (strstr($nameColumn, '.', false)) {
				$data = explode('.', $nameColumn);
				$whereSql .= ' `' . $data[0] . '`.`' . $data[1] . '` = ' . $value . ' AND';
			} else {
=======
			if (!empty($nameColumn) && !empty($value)) {
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556:framework/Database/PDOConnector.php
				$whereSql .= ' `' . $nameColumn . '` = :' . $nameColumn . ' AND';
			}
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
	 * @return int
	 */
	public function getLastId () {

		return self::$instance->lastInsertId();

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
	public static function closeConnection() {
		self::$instance = null;
	}

	private function __clone() {
		// TODO: Implement __clone() method.
	}

}