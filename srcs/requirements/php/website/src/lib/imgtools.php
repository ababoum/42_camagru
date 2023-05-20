<?php

namespace Application\Lib\ImgTools;

class ImgTools
{
    public static function super_impose(\GDimage $webcamImageResource, \GDimage $stickerImage, string $path): bool
    {
        // Get the dimensions of the sticker image
        $stickerWidth = imagesx($stickerImage);
        $stickerHeight = imagesy($stickerImage);

        // Get the dimensions of the webcam image
        $webcamWidth = imagesx($webcamImageResource);
        $webcamHeight = imagesy($webcamImageResource);

        // Calculate the desired dimensions for the resized sticker image
        $resizedWidth = intval(imagesx($webcamImageResource) * 0.1);
        $resizedHeight = intval(imagesy($webcamImageResource) * 0.1);
        $resizedDim = max($resizedWidth, $resizedHeight);

        // Resize the sticker image
        $resizedStickerImage = imagescale($stickerImage, $resizedDim, $resizedDim);

        // Calculate the position to overlay the sticker on the webcam image
        $overlayX = ($webcamWidth - $resizedDim) / 2;
        $overlayY = ($webcamHeight - $resizedDim) / 2;
        
        imagecopy($webcamImageResource, $resizedStickerImage, $overlayX, $overlayY, 0, 0, $resizedDim, $resizedDim);
        $ret = imagepng($webcamImageResource, $path);

        // Free up memory by destroying the image resources
        imagedestroy($webcamImageResource);
        imagedestroy($stickerImage);
        imagedestroy($resizedStickerImage);

        return $ret;
    }
}
