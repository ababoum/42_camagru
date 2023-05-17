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
        $postRepository = new PostRepository();
        $postRepository->connection = new DatabaseConnection();

        $new_img = ImgTools::super_impose($img, $filter);

        // Save image in uploads folder
        $new_img = base64_decode($new_img);
        $new_img_name = uniqid() . '.png';
        $new_img_path = 'uploads/' . $new_img_name;
        file_put_contents($new_img_path, $new_img);

        $postRepository->savePost($new_img_path, $user_id);
    }
}