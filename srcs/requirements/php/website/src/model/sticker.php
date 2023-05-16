<?php

namespace Application\Model\Sticker;

require_once('src/lib/database.php');

use Application\Lib\Database\DatabaseConnection;

class Sticker
{
    public string $title;
    public string $image_path;
    public string $id;
}

class StickerRepository
{
    public DatabaseConnection $connection;

    public function getStickers(): array
    {
        $statement = $this->connection->getConnection()->query(
            "SELECT id, title, image_path FROM stickers ORDER BY title ASC"
        );
        $stickers = [];
        while (($row = $statement->fetch())) {
            $sticker = new Sticker();
            $sticker->title = $row['title'];
            $sticker->image_path = $row['image_path'];
            $sticker->id = $row['id'];

            $stickers[] = $sticker;
        }

        return $stickers;
    }
}