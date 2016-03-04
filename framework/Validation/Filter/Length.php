<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 23:50
 */

namespace Framework\Validation\Filter;

/**
 * Class Length
 *
 * @package Framework\Validation\Filter
 */
class Length
{
	/**
	 * @var string
	 */
	private $error = '';

	/**
	 * @var int
	 */
	private $min;

	/**
	 * @var int
	 */
	private $max;

	/**
	 * Length constructor.
	 *
	 * @param $min
	 * @param $max
	 */
	public function __construct($min, $max) {
		$this->min = $min;
		$this->max = $max;
	}

	/**
	 * @return string
	 */
	public function getErrors () {
		return $this->error;
	}

	/**
	 * @param $nameField
	 * @param $value
	 *
	 * @return bool
	 */
	public function checkInput ($nameField, $value) {

		$length = strlen($value);
		$flag = true;

		if ($length < $this->min || $length > $this->max) {
			$this->error = 'Field ' . $nameField . ' must not be less than ' . $this->min
				. ' symbols and not larger than ' . $this->max;

			$flag = false;
		}

		return $flag;
	}
}