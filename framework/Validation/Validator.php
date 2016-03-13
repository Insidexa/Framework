<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 22:43
 */

namespace Framework\Validation;


use Framework\DI\Service;

/**
 * Class Validator
 *
 * @package Framework\Validation
 */
class Validator
{
	/**
	 * @var array
	 */
	private $errors = [];

	/**
	 * @var object
	 */
	private $model;

	/**
	 * @var string
	 */
	private $modelName;

	/**
	 * Validator constructor.
	 *
	 * @param $model
	 */
	public function __construct($model) {
		$this->model = $model;

		$reflection = new \ReflectionClass($model);
		$this->modelName = strtolower($reflection->getShortName());

		$this->check();
	}

	/**
	 * Check all properties
	 */
	private function check () {

		$rulesModel = $this->model->getRules();

		foreach ($rulesModel as $properties => $rules) {

			$messages = '';

			foreach ($rules as $filter) {
				$result = $filter->checkInput($properties, $this->model->$properties);
				if (!$result)
					$messages .= "<br>" . $filter->getErrors();
			}

			if (!empty($messages))
				$this->errors[$properties] = $messages;

		}

	}

	/**
	 * Return errors
	 *
	 * @return array
	 */
	public function getErrors () {
		return $this->errors;
	}

	/**
	 * Check exists errors and return true if exists
	 *
	 * @return bool
	 */
	public function isValid () {

		$isValid = false;
		Service::get('session')->set('validator.data', [
			$this->modelName => $this->model
		]);

		if (count($this->errors) === 0) {
			Service::get('session')->delete('validator.data');
			$isValid = true;
		}

		return $isValid;
	}
}