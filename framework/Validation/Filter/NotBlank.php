<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 23:50
 */

namespace Framework\Validation\Filter;

/**
 * Class NotBlank
 *
 * @package Framework\Validation\Filter
 */
class NotBlank implements FilterInterface
{
	/**
	 * @var string
	 */
	private $error = '';

	/**
	 * NotBlank constructor.
	 */
	public function __construct() {}

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

		$flag = true;

		if (empty($value)) {
			$this->error = 'Field ' . $nameField . ' empty';

			$flag = false;
		}

		return $flag;
	}
}