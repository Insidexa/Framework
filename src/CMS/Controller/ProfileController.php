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
use Framework\Exception\AuthLoginException;

/**
 * Class ProfileController
 *
 * @package CMS\Controller
 */
class ProfileController extends Controller {

	public function getAction () {

		if (Service::get('security')->isAuthenticated()) {
			$user = Service::get('security')->getUser();

			return $this->render('profile.html', [
				'user' => $user,
				'action' => $this->generateRoute('update_profile')
			]);
		}

		return $this->redirect('login', 'Please Login');
	}

	public function updateAction () {

		if (!Service::get('request')->isPost())
			throw new \Exception('Hack attempt');

		if (!Service::get('security')->isAuthenticated())
			return $this->redirect('login', 'Please Login');

		$errors = [];
		$userId = (int)$this->getRequest()->post('id');

		try {
			User::where([
				'id' => $userId
			])->update([
				'email' => $this->getRequest()->post('email'),
				'password' => $this->getRequest()->post('password')
			]);
		} catch(DatabaseException $e) {
			$errors[] = $e->getMessage();
		}

		$userId = Service::get('security')->getUser()->id;
		$user = User::find((int)$userId);

		Service::get('security')->setUser($user);

		return $this->render('profile.html', [
			'user' => $user,
			'action' => $this->generateRoute('update_profile'),
			'errors' => isset($errors) ? $errors : null
		]);

	}

}