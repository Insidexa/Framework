<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 03.02.16
 * Time: 21:17
 */

namespace Framework\Model;

use Framework\Database\PDOConnector;
use Framework\Exception\DatabaseException;

/**
 * Class ActiveRecord
 *
 * @package Framework\Database
 *
 * @author Jashka
 */
class ActiveRecordLayout extends PDOConnector {

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

	/**
	 * ActiveRecord constructor.
	 */
	public function __construct($data) {
		$this->dataModel = $data['objVars'];
		$this->pathNamespace = $data['pathNamespace'];
		$pathNamespace = explode('\\', $this->pathNamespace);
		$this->_model = end($pathNamespace);
		$this->setModel($this->_model);
		$this->_table = $data['table'];
	}

	/**
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
	 * @param $columnName
	 *
	 * @return bool
	 */
	private function findColumn($columnName) {

		foreach ($this->dataModel as $key => $value) {
			if ($columnName === 'id') return true;
			if ($key === $columnName) return true;
		}

		return false;

	}

	/**
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
	 * @return array
	 */
	private function getPropertiesAndValuesChildClass() {

		$reflection = new \ReflectionClass($this->pathNamespace);
		$classVars = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

		$vars = array_intersect_ukey($this->dataModel, $classVars, function ($key1, $key2) {
			if ($key1 === $key2)
				return 0;
		});

		return $vars;

	}

	/**
	 * @param $data
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return $this|array
	 */
	public function find($data) {

		switch ($data) {
			case is_int($data):
				$this->metaData = 'one';
				return $this->selectDB()
					->where(['id' => $data])->get();
				break;
			case is_string($data) && $data === 'all':
				return $this->select('*')->get();
				break;
			default:
				throw new \InvalidArgumentException('Invalid arguments');
				break;
		}

	}

	/**
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
	 * @return array|bool|null
	 */
	public function delete() {

		$this->type = 'delete';

		$this->deleteDB();

		return $this->query();

	}

	/**
	 * @return array|bool|null
	 * @throws DatabaseException
	 */
	public function query() {

		$stmp = $this->assemblyQuery();

		if (is_bool($stmp)) return $stmp;

		$this->data = $stmp->fetchAll();

		if ($this->metaData === 'one') {
			return $this->data[0];
		}

		if (count($this->data) === 0) {
			return [];
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