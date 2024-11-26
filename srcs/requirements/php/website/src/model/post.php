<?php

namespace Application\Model\Post;

require_once('src/lib/database.php');

use Application\Lib\Database\DatabaseConnection;
use PDOException;

class Post
{
    public string $title;
    public string $creation_date;
    public string $image_path;
    public int $id;
    public int $author_id;
    public int $nb_likes;
    public int $nb_comments;
    public bool $does_current_user_like_post = false;
}

class PostRepository
{
    public DatabaseConnection $connection;

    public function get_post_by_id(string $id): ?Post
    {
        try {
            $statement = $this->connection->getConnection()->prepare(
                "SELECT posts.id,
                        posts.title,
                        posts.image_path,
                        posts.user_id AS author_id,
                        TO_CHAR(posts.creation_date, 'DD Mon YYYY') AS creation_date,
                        COUNT(likes.id) AS number_of_likes
                FROM posts
                LEFT JOIN likes ON posts.id = likes.post_id
                WHERE posts.id = :id
                GROUP BY posts.id, posts.title, posts.image_path, posts.user_id, posts.creation_date"
            );
            $statement->bindValue(':id', $id, \PDO::PARAM_INT);
            $statement->execute();

            $row = $statement->fetch();
            if (!$row) {
                return null;
            }
            $post = new Post();
            $post->title = $row['title'];
            $post->creation_date = $row['creation_date'];
            $post->image_path = $row['image_path'];
            $post->id = intval($row['id']);
            $post->author_id = intval($row['author_id']);
            $post->nb_likes = intval($row['number_of_likes']);
            $post->nb_comments = 0; // is not used in this context
            $post->does_current_user_like_post = false; // is not used in this context

            return $post;
        } catch (PDOException $e) {
            error_log("Error in get_post_by_id: " . $e->getMessage());
            return null;
        }
    }

    public function get_nb_posts(): int
    {
        try {
            $statement = $this->connection->getConnection()->query(
                "SELECT COUNT(*) AS number_of_posts FROM posts"
            );
            $row = $statement->fetch();
            return intval($row['number_of_posts']);
        } catch (PDOException $e) {
            error_log("Error in get_nb_posts: " . $e->getMessage());
            return 0;
        }
    }

    public function get_posts_by_page(int $page_number, string $current_user_id): array
    {
        try {
            $statement = $this->connection->getConnection()->prepare(
                "SELECT posts.id,
                        posts.title,
                        posts.image_path,
                        posts.user_id AS author_id,
                        TO_CHAR(posts.creation_date, 'DD-Mon-YYYY at HH24:MI:SS') AS creation_date,
                        COUNT(likes.id) AS number_of_likes,
                        COUNT(comments.id) AS number_of_comments,
                        CASE WHEN COUNT(user_likes.id) > 0 THEN TRUE ELSE FALSE END AS user_likes_post
                FROM posts
                LEFT JOIN likes ON posts.id = likes.post_id
                LEFT JOIN likes AS user_likes ON likes.post_id = user_likes.post_id AND user_likes.user_id = :current_user_id
                LEFT JOIN comments ON posts.id = comments.post_id
                GROUP BY posts.id, posts.title, posts.image_path, posts.user_id, posts.creation_date
                ORDER BY posts.creation_date DESC
                LIMIT 5 OFFSET :page_index"
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
                $post->id = intval($row['id']);
                $post->author_id = intval($row['author_id']);
                $post->nb_likes = intval($row['number_of_likes']);
                $post->nb_comments = intval($row['number_of_comments']);
                $post->does_current_user_like_post = (bool)$row['user_likes_post'];

                $posts[] = $post;
            }

            return $posts;
        } catch (PDOException $e) {
            error_log("Error in get_posts_by_page: " . $e->getMessage());
            return [];
        }
    }

    public function save_post(string $image_path, string $user_id, string $username): bool
    {
        try {
            $statement = $this->connection->getConnection()->prepare(
                "INSERT INTO posts (user_id, title, image_path) VALUES (:user_id, :title, :image_path)"
            );
            $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
            $statement->bindValue(':title', 'Post by ' . $username, \PDO::PARAM_STR);
            $statement->bindValue(':image_path', $image_path, \PDO::PARAM_STR);
            return $statement->execute();
        } catch (PDOException $e) {
            error_log("Error in save_post: " . $e->getMessage());
            return false;
        }
    }

    public function get_posts_by_user_id(string $user_id): array
    {
        try {
            $statement = $this->connection->getConnection()->prepare(
                "SELECT id,
                    title,
                    image_path, 
                    TO_CHAR(creation_date, 'DD-Mon-YYYY at HH24:MI:SS') AS creation_date
                FROM posts
                WHERE user_id = :user_id
                ORDER BY creation_date DESC"
            );
            $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
            $statement->execute();

            $posts = [];
            while (($row = $statement->fetch())) {
                $post = new Post();
                $post->title = $row['title'];
                $post->creation_date = $row['creation_date'];
                $post->image_path = $row['image_path'];
                $post->id = intval($row['id']);

                $posts[] = $post;
            }

            return $posts;
        } catch (PDOException $e) {
            error_log("Error in get_posts_by_user_id: " . $e->getMessage());
            return [];
        }
    }

    public function delete_post(string $post_id): bool
    {
        try {
            $statement = $this->connection->getConnection()->prepare(
                "DELETE FROM posts WHERE id = :post_id"
            );
            $statement->bindValue(':post_id', $post_id, \PDO::PARAM_INT);
            return $statement->execute();
        } catch (PDOException $e) {
            error_log("Error in delete_post: " . $e->getMessage());
            return false;
        }
    }
}
