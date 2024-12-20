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
    public function __construct()
    {
        $this->connection = new DatabaseConnection();
    }

    public function show(string $post_id, string $current_user_id): void
    {
        $postRepository = new PostRepository();
        $postRepository->connection = new DatabaseConnection();

        $post = $postRepository->get_post_by_id($post_id);

        $commentRepository = new CommentRepository();
        $commentRepository->connection = new DatabaseConnection();
        $comments = $commentRepository->get_comments($post_id);

        require('templates/post.php');
    }

    public function delete_post(string $post_id, string $source, int $page = 1): void
    {
        $commentRepository = new CommentRepository();
        $commentRepository->connection = new DatabaseConnection();
        $commentRepository->delete_comments($post_id);

        $likeRepository = new LikeRepository();
        $likeRepository->connection = new DatabaseConnection();
        $likeRepository->delete_likes($post_id);

        $postRepository = new PostRepository();
        $postRepository->connection = new DatabaseConnection();
        $postRepository->delete_post($post_id);


        if ($source == 'cam') {
            header('Location: index.php?action=webcam');
        } else {
            header('Location: index.php?action=gallery&page=' . $page);
        }
    }

    public function like_post(string $post_id, string $liker_id, int $current_page): void
    {
        $likeRepository = new LikeRepository();
        $likeRepository->connection = new DatabaseConnection();
        $likeRepository->like_post($post_id, $liker_id);

        header('Location: index.php?action=gallery&page=' . $current_page);
    }

    public function unlike_post(string $post_id, string $liker_id, int $current_page): void
    {
        $likeRepository = new LikeRepository();
        $likeRepository->connection = new DatabaseConnection();
        $likeRepository->unlike_post($post_id, $liker_id);

        header('Location: index.php?action=gallery&page=' . $current_page);
    }
}