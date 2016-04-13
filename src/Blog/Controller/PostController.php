<?php
/**
 * Created by PhpStorm.
 * User: dgilan
 * Date: 10/15/14
 * Time: 12:49 PM
 */

namespace Blog\Controller;

use Blog\Model\Post;
use Blog\Model\UserPosts;
use Framework\Controller\Controller;
use Framework\DI\Service;
use Framework\Exception\DatabaseException;
use Framework\Exception\HttpNotFoundException;
use Framework\Request\Request;
use Framework\Response\Response;
use Framework\Validation\Validator;

class PostController extends Controller
{

    public function indexAction()
    {
        $id = null;
        if (Service::get('security')->isAuthenticated())
            $id = Service::get('security')->getUser()->id;

        return $this->render('index.html', array('posts' => Post::find('all')->with(UserPosts::class)->get(),
            'currentId' => $id));
    }

    public function getPostAction($id)
    {
        return new Response('Post: #'.$id);
    }

    public function addAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $post          = new Post();
                $date          = new \DateTime();
                $post->title   = $this->getRequest()->post('title');
                $post->content = trim($this->getRequest()->post('content'));
                $post->date    = $date->format('Y-m-d H:i:s');

                $validator = new Validator($post);
                if ($validator->isValid()) {
                    $lastId = $post->save();

                    $userPosts = new UserPosts();
                    $userPosts->post_id = (int)$lastId;
                    $userPosts->user_id = (int)Service::get('security')->getUser()->id;
                    $userPosts->save();

                    return $this->redirect($this->generateRoute('home'), 'The data has been saved successfully');
                } else {
                    $error = $validator->getErrors();
                }
            } catch(DatabaseException $e) {
                $error = $e->getMessage();
            }
        }

        return $this->render(
                    'add.html',
                    array('action' => $this->generateRoute('add_post'), 'errors' => isset($error)?$error:null)
        );
    }

    public function showAction($id)
    {
        if (!$post = Post::find((int)$id)) {
            throw new HttpNotFoundException('Page Not Found!');
        }
        return $this->render('show.html', array('post' => $post));
    }
}