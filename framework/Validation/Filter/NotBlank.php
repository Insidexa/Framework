<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 23:50
 */

namespace Framework\Validation\Filter;


class NotBlank
{
	private $error = '';

	public function __construct() {}

	public function getErrors () {
		return $this->error;
	}

	public function checkInput ($nameField, $value) {

		if (empty($value)) {
			$this->error = 'Field ' . $nameField . ' empty';

			return false;
		}

		return true;
	}
}