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
use Blog\Model\Post;
use Framework\Exception\DatabaseException;
use Framework\Request\Request;

/**
 * Class BlogController
 *
 * @package CMS\Controller
 */
class BlogController extends Controller{

	public function editAction ($id, Request $request) {

		$id = (int)$id;
		$errors = [];

		if ($request->isPost()) {
			try {
				$date = new \DateTime();

				$id = Post::where([
					'id' => $id
				])->update([
					'title' => $request->post('title'),
					'content' => $request->post('content'),
					'date' => $date->format('Y-m-d H:i:s')
				]);

				return $this->redirect($this->generateRoute('home'), 'The data has been updated successfully');

			} catch (DatabaseException $e) {
				$errors[] = $e->getMessage();
			}
		}

		return $this->render('edit.html', [
			'post' => Post::find($id),
			'action' => $this->generateRoute('edit_post', [
				'id' => $id
			]),
			'errors' => $errors ?? null
		]);

	}

}