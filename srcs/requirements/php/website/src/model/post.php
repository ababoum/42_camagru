<?php

namespace Application\Model\Post;

require_once('src/lib/database.php');

use Application\Lib\Database\DatabaseConnection;

class Post
{
    public string $title;
    public string $creation_date;
    public string $image_path;
    public string $id;
    public string $author_id;
    public int $nb_likes;
    public int $nb_comments;
    public bool $does_current_user_like_post = false;
}

class PostRepository
{
    public DatabaseConnection $connection;

    public function get_post_by_id(string $id): Post
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT posts.id,
                    posts.title,
                    posts.image_path,
                    posts.user_id AS author_id,
                    DATE_FORMAT(posts.creation_date, '%d %M %Y') AS creation_date,
                    COUNT(likes.id) AS number_of_likes
            FROM posts
            LEFT JOIN likes ON posts.id = likes.post_id
            WHERE posts.id = ?"
        );
        $statement->execute([$id]);

        $row = $statement->fetch();
        $post = new Post();
        $post->title = $row['title'];
        $post->creation_date = $row['creation_date'];
        $post->image_path = $row['image_path'];
        $post->id = $row['id'];
        $post->author_id = $row['author_id'];
        $post->nb_likes = $row['number_of_likes'];
        $post->nb_comments = 0; // is not used in this context
        $post->does_current_user_like_post = false; // is not used in this context

        return $post;
    }

    public function get_nb_posts(): int
    {
        $statement = $this->connection->getConnection()->query(
            "SELECT COUNT(*) AS number_of_posts FROM posts"
        );
        $row = $statement->fetch();
        return $row['number_of_posts'];
    }

    public function get_posts_by_page(int $page_number, string $current_user_id): array
    {
        // Returns the 5 posts of the page, with the number of likes, 
        // and whether the current user liked the post
        $statement = $this->connection->getConnection()->prepare(
            "SELECT posts.id,
                    posts.title,
                    posts.image_path,
                    posts.user_id AS author_id,
                    DATE_FORMAT(posts.creation_date, '%d-%M-%Y at %Hh%imin%ss') AS creation_date,
                    COUNT(likes.id) AS number_of_likes,
                    COUNT(comments.id) AS number_of_comments,
                    IF(COUNT(user_likes.id) > 0, 1, 0) AS user_likes_post
            FROM posts
            LEFT JOIN likes ON posts.id = likes.post_id
            LEFT JOIN likes AS user_likes ON likes.post_id = user_likes.post_id AND user_likes.user_id = :current_user_id
            LEFT JOIN comments ON posts.id = comments.post_id
            GROUP BY posts.id
            ORDER BY creation_date DESC
            LIMIT :page_index, 5"
        );

        $statement->bindValue(':current_user_id', $current_user_id, \PDO::PARAM_STR);
        $statement->bindValue(':page_index', ($page_number - 1) * 5, \PDO::PARAM_INT);
        $statement->execute();

        $posts = [];
        while (($row = $statement->fetch())) {
            $post = new Post();
            $post->title = $row['title'];
            $post->creation_date = $row['creation_date'];
            $post->image_path = $row['image_path'];
            $post->id = $row['id'];
            $post->author_id = $row['author_id'];
            $post->nb_likes = $row['number_of_likes'];
            $post->nb_comments = $row['number_of_comments'];
            $post->does_current_user_like_post = $row['user_likes_post'];

            $posts[] = $post;
        }

        return $posts;
    }

    public function save_post(string $image_path, string $user_id, string $username): void
    {
        $statement = $this->connection->getConnection()->prepare(
            "INSERT INTO posts (user_id, title, image_path) VALUES (?, ?, ?)"
        );
        $statement->execute([$user_id, 'Post by ' . $username, $image_path]);
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
            $post->creation_date = $row['creation_date'];
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