<?php

namespace Application\Controllers\Post;

require_once('src/lib/database.php');
require_once('src/model/comment.php');
require_once('src/model/post.php');
require_once('src/model/like.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\Comment\CommentRepository;
use Application\Model\Post\PostRepository;
use Application\Model\Like\LikeRepository;

class Post
{
    public function execute(string $id): void
    {
        $connection = new DatabaseConnection();

        $postRepository = new PostRepository();
        $postRepository->connection = $connection;
        $post = $postRepository->get_post_by_id($id);

        $commentRepository = new CommentRepository();
        $commentRepository->connection = $connection;
        $comments = $commentRepository->get_comments($id);

        require('templates/post.php');
    }

    public function delete_post(string $post_id, string $source): void
    {
        $connection = new DatabaseConnection();

        $postRepository = new PostRepository();
        $postRepository->connection = $connection;
        $postRepository->delete_post($post_id, $source);

        $commentRepository = new CommentRepository();
        $commentRepository->connection = $connection;
        $commentRepository->delete_comments($post_id);

        $likeRepository = new LikeRepository();
        $likeRepository->connection = $connection;
        $likeRepository->delete_likes($post_id);

        if ($source == 'cam') {
            header('Location: index.php?action=webcam');
        } else {
            header('Location: index.php');
        }
    }
}
