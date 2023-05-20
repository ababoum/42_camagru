<?php

namespace Application\Controllers\Homepage;

require_once('src/lib/database.php');
require_once('src/model/post.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\Post\PostRepository;

class Homepage
{
    public function execute()
    {
        $postRepository = new PostRepository();
        $postRepository->connection = new DatabaseConnection();
        $posts = $postRepository->get_posts();

        require('templates/homepage.php');
    }
}