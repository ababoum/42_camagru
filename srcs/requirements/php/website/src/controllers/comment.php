<?php

namespace Application\Controllers\Comment;

require_once('src/lib/database.php');
require_once('src/model/comment.php');
require_once('src/lib/mailingtools.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\Comment\CommentRepository;
use Application\Lib\MailingTools\MailingTools;
use Application\Model\User\UserRepository;
use Application\Model\User\User;

class Comment
{
    private function _notify_post_author(string $post_id)
    {
        $connection = new DatabaseConnection();
        $userRepository = new UserRepository();
        $userRepository->connection = $connection;

        $commentRepository = new CommentRepository();
        $commentRepository->connection = new DatabaseConnection();
        $post_author_id = $commentRepository->get_post_author_id($post_id);
        $user = $userRepository->get_user_by_id($post_author_id);

        MailingTools::notify_post_author($user->email, $user->username, $post_id);
    }
    public function add_comment(string $post_id, string $user_id, string $comment)
    {
        $commentRepository = new CommentRepository();
        $commentRepository->connection = new DatabaseConnection();
        $success = $commentRepository->create_comment($post_id, $user_id, $comment);

        if (!$success) {
            throw new \Exception('The comment cannot be added.');
        } else {
            if ($user_id !== $commentRepository->get_post_author_id($post_id))
                $this->_notify_post_author($post_id);
            header('Location: index.php?action=post&id=' . $post_id);
        }
    }

    public function delete_comment(string $comment_id, string $post_id, string $user_id)
    {
        $commentRepository = new CommentRepository();
        $commentRepository->connection = new DatabaseConnection();

        $comment = $commentRepository->get_comment($comment_id);
        if ($comment->author_id !== $user_id) {
            throw new \Exception('You cannot delete this comment.');
        }

        $success = $commentRepository->delete_comment($comment_id);

        if (!$success) {
            throw new \Exception('The comment cannot be deleted.');
        } else {
            if ($post_id === '0')
                header('Location: index.php?action=gallery');
            else
                header('Location: index.php?action=post&id=' . $post_id);
        }
    }
}