<?php

namespace Application\Lib\ImgTools;

class ImgTools
{
    public static function super_impose($img, $filter): string|bool
    {
        $img = \imagecreatefromstring($img);
        $filter = \imagecreatefrompng($filter);
        \imagecopy($img, $filter, 0, 0, 0, 0, 640, 480);
        \ob_start();
        \imagepng($img);
        $img = \ob_get_clean();
        if ($img === false)
            return (false);
        else {
            $img = \base64_encode($img);
            return ($img);
        }
    }
}
