<?php

namespace Application\Model\Comment;

require_once('src/lib/database.php');

use Application\Lib\Database\DatabaseConnection;

class Comment
{
    public string $id;
    public string $author;
    public string $creationDate;
    public string $comment;
    public string $post;
}

class CommentRepository
{
    public DatabaseConnection $connection;

    public function getComments(string $post): array
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT c.id, u.username AS author, comment, DATE_FORMAT(creation_date, '%d/%M/%Y at %Hh%imin%ss') AS creation_date, post_id FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE post_id = ? ORDER BY creation_date DESC"
        );
        $statement->execute([$post]);

        $comments = [];
        while (($row = $statement->fetch())) {
            $comment = new Comment();
            $comment->id = $row['id'];
            $comment->author = $row['author'];
            $comment->creationDate = $row['creation_date'];
            $comment->comment = $row['comment'];
            $comment->post = $row['post_id'];

            $comments[] = $comment;
        }

        return $comments;
    }

    public function getComment(string $id): ?Comment
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT c.id, u.username AS author, comment, DATE_FORMAT(creation_date, '%d/%M/%Y at %Hh%imin%ss') AS creation_date, post_id FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.id = ?"
        );
        $statement->execute([$id]);

        $row = $statement->fetch();
        if ($row === false) {
            return null;
        }

        $comment = new Comment();
        $comment->id = $row['id'];
        $comment->author = $row['author'];
        $comment->creationDate = $row['creation_date'];
        $comment->comment = $row['comment'];
        $comment->post = $row['post_id'];

        return $comment;
    }

    public function createComment(string $post, string $author, string $comment): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'INSERT INTO comments(post_id, author, comment, comment_date) VALUES(?, ?, ?, NOW())'
        );
        $affectedLines = $statement->execute([$post, $author, $comment]);

        return ($affectedLines > 0);
    }
}