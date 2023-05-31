<?php

namespace Application\Controllers\Webcam;

require_once('src/lib/database.php');
require_once('src/model/sticker.php');
require_once('src/model/post.php');
require_once('src/lib/imgtools.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\Sticker\StickerRepository;
use Application\Model\Post\PostRepository;
use Application\Model\User\UserRepository;
use \Application\Lib\ImgTools\ImgTools;

class Webcam
{
    public function execute()
    {
        $stickerRepository = new StickerRepository();
        $stickerRepository->connection = new DatabaseConnection();
        $stickers = $stickerRepository->getStickers();

        // Prepare posts of current user
        $postRepository = new PostRepository();
        $postRepository->connection = new DatabaseConnection();

        $posts = $postRepository->get_posts_by_user_id($_SESSION['id']);

        require('templates/webcam.php');
    }

    public function save_shot(string $img, string $filter, string $user_id)
    {
        // Check images extension
        $img = base64_decode($img);
        if ($img === false)
            throw new \Exception('Error while decoding image (snapshot)');
        // Check if the image is less than 5MB
        if (strlen($img) > 5000000)
            throw new \Exception('Image is too big (snapshot)');
        // Check if the image is a valid image
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $img, FILEINFO_MIME_TYPE);
        if ($mime_type !== 'image/png')
            throw new \Exception('Image is not a valid image (snapshot)');
        // Generate a GD image
        $img = imagecreatefromstring($img);
        if ($img === false)
            throw new \Exception('Error while creating image from string (snapshot)');

        $filter = imagecreatefrompng($filter);
        if ($filter === false)
            throw new \Exception('Error while creating image from png (filter)');

        $postRepository = new PostRepository();
        $postRepository->connection = new DatabaseConnection();

        // Superimpose images and save final image in uploads folder
        $new_img_name = uniqid() . '.png';
        $new_img_path = 'uploads/' . $new_img_name;

        if (ImgTools::super_impose($img, $filter, $new_img_path) === false)
            throw new \Exception('Error while superimposing image');

        // Fetch the user's username
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();

        $user = $userRepository->get_user_by_id($user_id);
        $postRepository->save_post($new_img_path, $user_id, $user->username);

        // Redirect to gallery
        header('Location: index.php?action=webcam');
    }
}
