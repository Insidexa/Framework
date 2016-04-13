<?php
/**
 * Created by PhpStorm.
 * User: dgilan
 * Date: 10/16/14
 * Time: 11:36 AM
 */

namespace Blog\Model;

use Framework\Model\ActiveRecord;

/**
 * Class UserPosts
 *
 * @package Blog\Model
 */
class UserPosts extends ActiveRecord
{
    public $user_id;
    public $post_id;

    public static $withModel = [User::class, Post::class];

    public static $conditions = 'user_id';

    public static function getTable()
    {
        return 'getPostUser';
    }
}