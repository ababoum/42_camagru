<?php

namespace Application\Controllers\Gallery;

require_once('src/lib/database.php');
require_once('src/model/post.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\Post\PostRepository;

class Gallery
{
    public function execute_page(int $page, string $current_user_id)
    {
        $postRepository = new PostRepository();
        $postRepository->connection = new DatabaseConnection();
        $posts = $postRepository->get_posts_by_page($page, $current_user_id);
        $nb_posts = $postRepository->get_nb_posts();

        $nb_of_pages = ceil($nb_posts / 5);

        if (
            $nb_posts > 0 &&
            ($page > $nb_of_pages || $page < 1)
        ) {
            header('Location: index.php?action=gallery&page=1');
            exit();
        }

        require('templates/gallery.php');
    }
}