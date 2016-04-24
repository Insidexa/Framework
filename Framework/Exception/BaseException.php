<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 17.03.16
 * Time: 18:49
 */

namespace Framework\Exception;

/**
 * Class BaseException
 *
 * @package Framework\Exception
 */
class BaseException extends \Exception{

	/**
	 * @var int default code
	 */
	protected $code = 500;

}