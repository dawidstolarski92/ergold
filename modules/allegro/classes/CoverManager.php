<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class CoverManagerCore
{
    const ERROR_FILE_NOT_EXIST 	= 1;
    const ERROR_INALID_EXT 		= 2;
    const ERROR_INALID_FRAME 	= 3;

    public static function merge($base_image, $frame_image, $dst, &$error = 0, $scale_type = 0)
    {
        /**
        * 0 - Auto
        * 1 - Width
        * 2 - Height
        */
        // $scale_type = 0;
        $scale_out = false;

        $margin_top = 120;
        $margin_right = 0;
        $margin_bottom = 0;
        $margin_left = 0;

        $frame = $frame_image;
        $image = $base_image;

        if (!file_exists($base_image) || !filesize($base_image) || !file_exists($frame_image) || !filesize($frame_image)) {
            return !($error = self::ERROR_FILE_NOT_EXIST);
        }

        // Get image infos
        list($imageWidth, $imageHeight, $imageExt) = getimagesize($image);
        list($frameWidth, $frameHeight, $farmeExt) = getimagesize($frame);

        // Check frame
        if(!$frameWidth || !$frameHeight || $farmeExt != 3 /*PNG*/) {
        	return !($error = self::ERROR_INALID_FRAME);
        }

        // Scale factor
        if($scale_type === 1)
            $sacele = $frameWidth / $imageWidth;
        else if($scale_type === 2)
            $sacele = $frameHeight / $imageHeight;
        else
            if($scale_out)
                $sacele = max(($frameHeight / $imageHeight), ($frameWidth / $imageWidth));
            else
                $sacele = min((($frameHeight - $margin_top - $margin_bottom) / $imageHeight), (($frameWidth - $margin_right - $margin_left) / $imageWidth));

        $newHeight = $imageHeight * $sacele;
        $newWidth = $imageWidth * $sacele;

        // Load
        $thumb = imagecreatetruecolor($frameWidth, $frameHeight);

        switch ($imageExt) {
            case 1 :
                $source = imagecreatefromgif($image);
            break;

            case 2 :
                $source = imagecreatefromjpeg($image);
            break;

            case 3 :
                $source = imagecreatefrompng($image);
            break;

            default:
            	return !($error = self::ERROR_INALID_EXT);
            break;
        }

        // Set background to white
        $white = imagecolorallocate($thumb, 255, 255, 255);
        imagefill($thumb, 0, 0, $white);

        $top_position = $margin_top ? $margin_top : ($frameHeight / 2) - ($newHeight / 2);
        $left_position = $margin_left ? $margin_left : ($frameWidth / 2) - ($newWidth / 2);

        // Resize
        imagecopyresized($thumb, $source, $left_position, $top_position, 0, 0, $newWidth, $newHeight, $imageWidth, $imageHeight);

        // Merge
        imagealphablending($thumb, true);
        imagesavealpha($thumb, true);
        imagecopy($thumb, imagecreatefrompng($frame), 0, 0, 0, 0, $frameWidth, $frameHeight);

        // Save
        imagepng($thumb, $dst);

        // Clean
        imagedestroy($thumb);

        return true;
    }
}