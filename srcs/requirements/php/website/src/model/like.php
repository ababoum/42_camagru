<?php

namespace Application\Model\Like;

require_once('src/lib/database.php');

use Application\Lib\Database\DatabaseConnection;

class Like
{
    public string $id;
    public string $liker;
    public string $creation_date;
    public string $post_id;
}

class LikeRepository
{
    public DatabaseConnection $connection;

    public function like_post(string $post_id, string $liker_id): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            "INSERT INTO likes (post_id, user_id) VALUES (?, ?)"
        );

        return $statement->execute([$post_id, $liker_id]);
    }

    public function unlike_post(string $post_id, string $liker_id): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            "DELETE FROM likes WHERE post_id = ? AND user_id = ?"
        );

        return $statement->execute([$post_id, $liker_id]);
    }

    public function delete_likes(string $post_id): void
    {
        $statement = $this->connection->getConnection()->prepare(
            "DELETE FROM likes WHERE post_id = ?"
        );
        $statement->execute([$post_id]);
    }
}
