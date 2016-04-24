<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 13.03.16
 * Time: 22:09
 */

namespace Framework\Validation\Filter;


interface FilterInterface {

	public function getErrors ();

	public function checkInput ($nameField, $value);

}