<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 22:43
 */

namespace Framework\Validation;


class Validator
{
	private $errors = [];
	private $model;

	public function __construct($model) {
		$this->model = $model;

		$this->check();
	}

	/**
	 * Check all properties
	 */

	private function check () {

		$rulesModel = $this->model->getRules();

		foreach ($rulesModel as $properties => $rules) {

			foreach ($rules as $filter) {
				$result = $filter->checkInput($this->model->$properties);
				if (!$result)
					$this->errors[$properties] .= "<br>" . $filter->getErrors();
			}

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
		$isErrors = false;
		if (count($this->errors) === 0) {
			$isErrors = true;
		}

		return $isErrors;
	}
}