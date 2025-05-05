<?php

function uploadFileHandler($fileKey, $oldFile, $dest, $targetWidth = 0, $targetHeight = 0)
{
    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES[$fileKey]['tmp_name'];
        $originalName = basename($_FILES[$fileKey]['name']);

        // Get image info
        $imageInfo = getimagesize($tmpName);
        if (!$imageInfo) {
            return "Uploaded file for '$fileKey' is not a valid image.";
        }

        list($width, $height, $type) = $imageInfo;
        $mime = $imageInfo['mime'];
        $ext = image_type_to_extension($type, false);

        // Load the image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = imagecreatefromjpeg($tmpName);
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($tmpName);
                break;
            case IMAGETYPE_GIF:
                $srcImage = imagecreatefromgif($tmpName);
                break;
            default:
                return "Unsupported image type for '$fileKey'.";
        }

        // Resize if needed
        if ($targetWidth > 0 && $targetHeight > 0) {
            $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

            // Preserve transparency for PNG & GIF
            if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
                imagecolortransparent($resizedImage, imagecolorallocatealpha($resizedImage, 0, 0, 0, 127));
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
            }

            imagecopyresampled($resizedImage, $srcImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        } else {
            $resizedImage = $srcImage;
        }

        // Create destination folder if not exists
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }

        // Save image
        $safeKey = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower($fileKey));
        $newName = $safeKey . '_img_' . uniqid() . '.' . $ext;
        $destinationPath = rtrim($dest, '/') . '/' . $newName;

        $saveSuccess = false;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $saveSuccess = imagejpeg($resizedImage, $destinationPath, 90);
                break;
            case IMAGETYPE_PNG:
                $saveSuccess = imagepng($resizedImage, $destinationPath);
                break;
            case IMAGETYPE_GIF:
                $saveSuccess = imagegif($resizedImage, $destinationPath);
                break;
        }

        // Clean up
        imagedestroy($srcImage);
        if ($resizedImage !== $srcImage) {
            imagedestroy($resizedImage);
        }

        if ($saveSuccess) {
            // Delete old file
            if (!empty($oldFile) && file_exists($dest . $oldFile) && $oldFile !== $newName) {
                unlink($dest . $oldFile);
            }
            return $newName;
        } else {
            return "Error saving resized image for '$fileKey'.";
        }
    }

    // No new upload, return old file
    return $oldFile;
}
?>
