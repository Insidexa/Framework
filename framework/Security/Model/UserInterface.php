<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 25.01.16
 * Time: 12:00
 */

namespace Framework\Security\Model;

interface UserInterface {

	public static function getTable();

	public function getRole();

}