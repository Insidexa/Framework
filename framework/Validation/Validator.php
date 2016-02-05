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

	private function check () {

		$rulesModel = $this->model->getRules();

		foreach ($rulesModel as $properties => $rules) {

			$messages = '';

			foreach ($rules as $filter) {
				$result = $filter->checkInput($properties, $this->model->$properties);
				if (!$result)
					$messages .= "<br>" . $filter->getErrors();
			}

			$this->errors[$properties] = $messages;

		}

	}

	public function getErrors () {
		return $this->errors;
	}

	public function isValid () {
		$isErrors = false;
		if (count($this->errors) === 0) {
			$isErrors = true;
		}

		return $isErrors;
	}
}