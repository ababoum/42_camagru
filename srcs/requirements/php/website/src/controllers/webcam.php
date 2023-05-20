<?php

namespace Application\Controllers\Webcam;

require_once('src/lib/database.php');
require_once('src/model/sticker.php');
require_once('src/model/post.php');
require_once('src/lib/imgtools.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\Sticker\StickerRepository;
use Application\Model\Post\PostRepository;
use \Application\Lib\ImgTools\ImgTools;

class Webcam
{
    public function execute()
    {
        $stickerRepository = new StickerRepository();
        $stickerRepository->connection = new DatabaseConnection();
        $stickers = $stickerRepository->getStickers();

        require('templates/webcam.php');
    }

    public function save_shot($img, $filter, $user_id)
    {
        // Check images extension
        $img = base64_decode($img);
        if ($img === false)
            throw new \Exception('Error while decoding image (snapshot)');
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
        
        if ( ImgTools::super_impose($img, $filter, $new_img_path) === false)
            throw new \Exception('Error while superimposing image');

        $postRepository->savePost($new_img_path, $user_id);

        // Redirect to gallery
        header('Location: index.php');
    }
}