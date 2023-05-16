<?php

namespace Application\Model\Post;

require_once('src/lib/database.php');

use Application\Lib\Database\DatabaseConnection;

class Post
{
    public string $title;
    public string $creationDate;
    public string $image_path;
    public string $id;
}

class PostRepository
{
    public DatabaseConnection $connection;

    public function getPost(string $id): Post
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id, title, image_path, DATE_FORMAT(creation_date, '%d/%M/%Y at %Hh%imin%ss') AS creation_date FROM posts WHERE id = ?"
        );
        $statement->execute([$id]);

        $row = $statement->fetch();
        $post = new Post();
        $post->title = $row['title'];
        $post->creationDate = $row['creation_date'];
        $post->image_path = $row['image_path'];
        $post->id = $row['id'];

        return $post;
    }

    public function getPosts(): array
    {
        $statement = $this->connection->getConnection()->query(
            "SELECT id, title, image_path, DATE_FORMAT(creation_date, '%d/%M/%Y at %Hh%imin%ss') AS creation_date FROM posts ORDER BY creation_date DESC LIMIT 0, 5"
        );
        $posts = [];
        while (($row = $statement->fetch())) {
            $post = new Post();
            $post->title = $row['title'];
            $post->creationDate = $row['creation_date'];
            $post->image_path = $row['image_path'];
            $post->id = $row['id'];

            $posts[] = $post;
        }

        return $posts;
    }
}