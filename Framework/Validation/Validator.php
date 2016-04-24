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

<<<<<<< HEAD
	/**
	 * Check all properties
	 */
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
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

<<<<<<< HEAD
	/**
	 * Return errors
	 *
	 * @return array
	 */
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	public function getErrors () {
		return $this->errors;
	}

<<<<<<< HEAD
	/**
	 * Check exists errors and return true if exists
	 *
	 * @return bool
	 */
=======
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
	public function isValid () {
		$isErrors = false;
		if (count($this->errors) === 0) {
			$isErrors = true;
		}

		return $isErrors;
	}
}