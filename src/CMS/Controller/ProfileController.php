<?php
/**
 * Created by PhpStorm.
 * User: jashka
 * Email: nostresss77@gmail.com
 * Date: 13.03.16
 * Time: 19:36
 */

namespace CMS\Controller;

use Blog\Model\User;
use Framework\ {
	Controller\Controller,
	DI\Service,
	Exception\DatabaseException
};

/**
 * Class ProfileController
 *
 * @package CMS\Controller
 */
class ProfileController extends Controller {

	public function getAction () {

		$user = Service::get('security')->getUser();

		return $this->render('profile.html', [
			'user' => $user,
			'action' => $this->generateRoute('update_profile')
		]);

	}

	public function updateAction () {

		$error = [];

		try {
			User::where([
				'id' => (int)$this->getRequest()->post('id')
			])->update([
				'email' => $this->getRequest()->post('email'),
				'password' => $this->getRequest()->post('password')
			]);
		} catch(DatabaseException $e) {
			$error[] = $e->getMessage();
		}

		// Мне логично после апдейта юзера его разлогинивать
		Service::get('security')->clear();

		return $this->redirect($this->generateRoute('login'), $error);

	}

}