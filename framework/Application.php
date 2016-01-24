<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Date: 24.01.16
 * Time: 14:42
 */

class Application
{

	public function run () {
		new \Framework\DI\ServiceLocator\Service();
	}

}