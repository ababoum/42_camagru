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
    public DatabaseConnection $connection;

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
        // Decode the base64 image
        $img = base64_decode($img);
        if ($img === false) {
            throw new \Exception('Error while decoding image (snapshot)');
        }
    
        // Check if the image is within the size limit (5 MB)
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        if (strlen($img) > $maxFileSize) {
            throw new \Exception('Image is too large. Maximum size is 5MB.');
        }
    
        // Validate the image type
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $img, FILEINFO_MIME_TYPE);
        $allowedMimeTypes = ['image/jpeg', 'image/png'];
        if (!in_array($mime_type, $allowedMimeTypes)) {
            throw new \Exception('Invalid image type. Only JPEG and PNG are allowed.');
        }
    
        // Generate a GD image
        $img = @imagecreatefromstring($img);
        if ($img === false) {
            throw new \Exception('Error while creating image from string (snapshot)');
        }
    
        // Check image dimensions
        $maxWidth = 2000;
        $maxHeight = 2000;
        $width = imagesx($img);
        $height = imagesy($img);
        if ($width > $maxWidth || $height > $maxHeight) {
            throw new \Exception("Image dimensions exceed the maximum allowed (${maxWidth}x${maxHeight} pixels).");
        }
    
        // Validate and load the filter image
        if (!file_exists($filter) || !is_readable($filter)) {
            throw new \Exception('Invalid or inaccessible filter image.');
        }
        $filterInfo = getimagesize($filter);
        if ($filterInfo === false || $filterInfo[2] !== IMAGETYPE_PNG) {
            throw new \Exception('Invalid filter image format. Only PNG is allowed.');
        }
        $filter = @imagecreatefrompng($filter);
        if ($filter === false) {
            throw new \Exception('Error while creating image from png (filter)');
        }
    
        $postRepository = new PostRepository();
        $postRepository->connection = new DatabaseConnection();
    
        // Generate a unique filename with a secure random string
        $new_img_name = bin2hex(random_bytes(16)) . '.png';
        $new_img_path = 'uploads/users_source/' . $new_img_name;
    
        // Ensure the uploads directory exists and is writable
        if (!is_dir('uploads/users_source') || !is_writable('uploads/users_source')) {
            throw new \Exception('Upload directory is not accessible or writable.');
        }
    
        // Superimpose images and save final image in uploads folder
        if (ImgTools::super_impose($img, $filter, $new_img_path) === false) {
            throw new \Exception('Error while superimposing image');
        }
    
        // Verify the saved image
        if (!file_exists($new_img_path) || filesize($new_img_path) === 0) {
            throw new \Exception('Failed to save the image.');
        }
    
        // Fetch the user's username
        $userRepository = new UserRepository();
        $userRepository->connection = new DatabaseConnection();
    
        $user = $userRepository->get_user_by_id($user_id);
        $postRepository->save_post($new_img_path, $user_id, $user->username);
    
        // Clean up resources
        imagedestroy($img);
        imagedestroy($filter);
    
        // Redirect to gallery
        header('Location: index.php?action=webcam');
        exit();
    }
    
}
