<?php

namespace Application\Controllers\Webcam;

require_once('src/lib/database.php');
require_once('src/model/sticker.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\Sticker\StickerRepository;

class Webcam
{
    public function execute()
    {
        $stickerRepository = new StickerRepository();
        $stickerRepository->connection = new DatabaseConnection();
        $stickers = $stickerRepository->getStickers();

        require('templates/webcam.php');
    }
}