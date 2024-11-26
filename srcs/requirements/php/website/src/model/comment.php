<?php

namespace Application\Model\Comment;

require_once('src/lib/database.php');

use Application\Lib\Database\DatabaseConnection;

class Comment
{
    public string $id;
    public string $author_id;
    public string $author;
    public string $creation_date;
    public string $comment;
    public string $post_id;
}

class CommentRepository
{
    public DatabaseConnection $connection;

    public function get_comments(string $post_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT c.id, u.username AS author, c.user_id, comment,
                TO_CHAR(c.creation_date, 'DD-Mon-YYYY at HH24:MI:SS') AS creation_date,
                post_id
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE post_id = ?
            ORDER BY creation_date DESC"
        );
        $statement->execute([$post_id]);

        $comments = [];
        while (($row = $statement->fetch())) {
            $comment = new Comment();
            $comment->id = $row['id'];
            $comment->author_id = $row['user_id'];
            $comment->author = $row['author'];
            $comment->post_id = $row['post_id'];
            $comment->creation_date = $row['creation_date'];
            $comment->comment = $row['comment'];

            $comments[] = $comment;
        }

        return $comments;
    }

    public function get_comment(string $comment_id): Comment
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT c.id, c.user_id, c.post_id, c.comment,
                u.username AS author,
                TO_CHAR(c.creation_date, 'DD-Mon-YYYY at HH24:MI:SS') AS creation_date
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.id = ?"
        );
        $statement->execute([$comment_id]);

        $row = $statement->fetch();
        $comment = new Comment();
        $comment->id = $row['id'];
        $comment->author_id = $row['user_id'];
        $comment->author = $row['author'];
        $comment->post_id = $row['post_id'];
        $comment->creation_date = $row['creation_date'];
        $comment->comment = $row['comment'];

        return $comment;
    }

    public function get_post_author_id(string $post_id): string
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT user_id FROM posts WHERE id = ?"
        );
        $statement->execute([$post_id]);

        $row = $statement->fetch();
        return $row['user_id'];
    }

    public function create_comment(string $post_id, string $author_id, string $comment): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'INSERT INTO comments(post_id, user_id, comment)
            VALUES(?, ?, ?)'
        );
        $affectedLines = $statement->execute([$post_id, $author_id, $comment]);

        return ($affectedLines > 0);
    }

    public function delete_comment(string $comment_id): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'DELETE FROM comments WHERE id = ?'
        );
        $statement->execute([$comment_id]);

        return ($statement->rowCount() > 0);
    }

    public function delete_comments(string $post_id): void
    {
        $statement = $this->connection->getConnection()->prepare(
            'DELETE FROM comments WHERE post_id = ?'
        );
        $statement->execute([$post_id]);
    }
}
