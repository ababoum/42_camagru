<?php

namespace Application\Controllers\Post;

require_once('src/lib/database.php');
require_once('src/model/comment.php');
require_once('src/model/post.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\Comment\CommentRepository;
use Application\Model\Post\PostRepository;

class Post
{
    public function execute(string $id)
    {
        $connection = new DatabaseConnection();

        $postRepository = new PostRepository();
        $postRepository->connection = $connection;
        $post = $postRepository->getPost($id);

        $commentRepository = new CommentRepository();
        $commentRepository->connection = $connection;
        $comments = $commentRepository->getComments($id);

        require('templates/post.php');
    }
}