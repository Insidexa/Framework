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
 * Class Length
 *
 * @package Framework\Validation\Filter
 */
class Length implements FilterInterface
=======

class Length
>>>>>>> 78ed7758dbc88d096d03ce590072885c94255556
{
	private $error = '';

	private $min;

	private $max;

	public function __construct($min, $max) {
		$this->min = $min;
		$this->max = $max;
	}

	public function getErrors () {
		return $this->error;
	}

	public function checkInput ($nameField, $value) {

		$length = strlen($value);

		if ($length < $this->min || $length > $this->max) {
			$this->error = 'Field ' . $nameField . ' must not be less than ' . $this->min
				. ' symbols and not larger than ' . $this->max;

			return false;
		}

		return true;
	}
}