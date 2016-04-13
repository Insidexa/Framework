<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 23:50
 */

namespace Framework\Validation\Filter;

<<<<<<< HEAD
/**
 * Class NotBlank
 *
 * @package Framework\Validation\Filter
 */
class NotBlank implements FilterInterface
=======

class NotBlank
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
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