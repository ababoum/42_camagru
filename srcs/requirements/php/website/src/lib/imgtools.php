<?php

namespace Application\Lib\ImgTools;

class ImgTools
{
    public static function super_impose(\GDimage $webcamImageResource, \GDimage $stickerImage, string $path, float $pos_x = 0.5, float $pos_y = 0.5, float $stickerSize = 0.2): bool {
        // Verify both image resources are valid
        if (!$webcamImageResource || !$stickerImage) {
            throw new \Exception('Invalid image resources provided');
        }
    
        $webcamWidth = imagesx($webcamImageResource);
        $webcamHeight = imagesy($webcamImageResource);
    
        // Convert relative values (0-1) to actual pixels
        $actualX = $pos_x * $webcamWidth;
        $actualY = $pos_y * $webcamHeight;
        $actualSize = $stickerSize * $webcamWidth;
    
        // Create resized sticker image
        $resizedStickerImage = imagecreatetruecolor($actualSize, $actualSize);
        if (!$resizedStickerImage) {
            throw new \Exception('Failed to create resized sticker image');
        }
    
        // Enable alpha blending and save alpha channel
        imagealphablending($resizedStickerImage, true);
        imagesavealpha($resizedStickerImage, true);
        $transparent = imagecolorallocatealpha($resizedStickerImage, 0, 0, 0, 127);
        imagefill($resizedStickerImage, 0, 0, $transparent);
    
        // Resize sticker
        if (!imagecopyresampled($resizedStickerImage, $stickerImage, 0, 0, 0, 0, $actualSize, $actualSize, imagesx($stickerImage), imagesy($stickerImage))) {
            throw new \Exception('Failed to resize sticker image');
        }
    
        // Enable alpha blending for webcam image
        imagealphablending($webcamImageResource, true);
        imagesavealpha($webcamImageResource, true);
    
        // Copy the resized sticker onto the webcam image
        if (!imagecopy($webcamImageResource, $resizedStickerImage, $actualX, $actualY, 0, 0, $actualSize, $actualSize)) {
            throw new \Exception('Failed to copy sticker onto image');
        }
    
        // Save the result
        $ret = imagepng($webcamImageResource, $path);
    
        // Clean up
        imagedestroy($resizedStickerImage);
    
        return $ret;
    }
       
}
