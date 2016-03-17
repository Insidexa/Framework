<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 16.03.16
 * Time: 8:48
 */

namespace CMS\Controller;

use Framework\ {
	Controller\Controller,
	Response\Response
};

/**
 * Class BlogController
 *
 * @package CMS\Controller
 */
class BlogController extends Controller{

	public function editAction ($id) {

		return new Response(__METHOD__ . ': ' . (int)$id);

	}

}