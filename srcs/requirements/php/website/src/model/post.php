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

    public function get_post_by_id(string $id): Post
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

    public function get_posts(): array
    {
        // Returns the 5 most recent posts
        $statement = $this->connection->getConnection()->query(
            "SELECT id, title, image_path, DATE_FORMAT(creation_date, '%d-%M-%Y at %Hh%imin%ss') AS creation_date FROM posts ORDER BY creation_date DESC LIMIT 0, 5"
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

    public function get_number_of_posts(): int
    {
        $statement = $this->connection->getConnection()->query(
            "SELECT COUNT(*) AS number_of_posts FROM posts"
        );
        $row = $statement->fetch();
        return $row['number_of_posts'];
    }

    public function get_posts_by_page(int $page_number): array
    {
        // Returns the 5 posts of the page, with the number of likes
        $statement = $this->connection->getConnection()->prepare(
            "SELECT posts.id, posts.title, posts.image_path,
                DATE_FORMAT(posts.creation_date, '%d-%M-%Y at %Hh%imin%ss') AS creation_date,
                COUNT(likes.id) AS number_of_likes
            FROM posts
            LEFT JOIN likes ON posts.id = likes.post_id
            GROUP BY posts.id
            ORDER BY creation_date DESC
            LIMIT ?, 5"
        );
        
        $statement->execute([($page_number - 1) * 5]);

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

    public function save_post(string $image_path, $user_id): void
    {
        $statement = $this->connection->getConnection()->prepare(
            "INSERT INTO posts (user_id, image_path) VALUES (?, ?)"
        );
        $statement->execute([$user_id, $image_path]);
    }

    public function get_posts_by_user_id(string $user_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT id, title, image_path, DATE_FORMAT(creation_date, '%d-%M-%Y at %Hh%imin%ss') AS creation_date
            FROM posts
            WHERE user_id = ?
            ORDER BY creation_date DESC"
        );
        $statement->execute([$user_id]);

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

    public function delete_post(string $post_id): void
    {
        $statement = $this->connection->getConnection()->prepare(
            "DELETE FROM posts WHERE id = ?"
        );
        $statement->execute([$post_id]);
    }
}
