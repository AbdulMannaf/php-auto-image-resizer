<?php
/**
 * **********************************************************************************************************************************************************
 * ## ImageResizer
 *
 * PHP library to resize any image in any aspect ratio without cropping, stretching and losing the quality of the image by adding any color around the image.
 *
 * @author Abdul Mannaf
 * @version 1.1
 * @license MIT
 *
 * **********************************************************************************************************************************************************
 */

class ImageResizer
{
    private $source_image, $output_image, $source_image_data;
    private $aspect_ratio = [1, 1];
    private $ratio = 1;
    private $transparent_level = 0;
    private $compression_level = 5;
    private $image_quality = 80;
    private $background_color = array(
        "R" => 255,
        "G" => 255,
        "B" => 255,
    );
    private $valid_image_types_and_mime = [
        "png", "jpg", "jpeg", "gif",
        "image/png", "image/jpeg", "image/pjpeg", "image/gif",
    ];

    /**
     * ### Class Constructor
     *
     * @param string $source_image - Source image path.
     * @param string $output_image - Output image path without extension.
     */

    public function __construct(string $source_image = '', string $output_image = '')
    {
        if (trim($source_image) != '') {
            $this->setSourceImage($source_image);
        }
        if (trim($output_image) != '') {
            $this->setOutputImage($output_image);
        }
        return true;
    }

    /**
     * ### Set / Change source image path.
     *
     * @param string $source_image - Source image path.
     * @return void
     */

    public function setSourceImage(string $source_image)
    {
        if (trim($source_image) != '') {
            if (file_exists($source_image)) {
                $this->source_image = $source_image;
                $this->source_image_data = file_get_contents($this->source_image);
                return true;
            } else {
                throw new Exception("Source image {$this->source_image} not exists.");
            }
        } else {
            throw new Exception("Source Image Path Not Provided.");
        }
    }

    /**
     * ### Set / Change output image path.
     *
     * @param string $output_image - Output image path.
     * @return void
     */

    public function setOutputImage(string $output_image)
    {
        if ($output_image != '') {
            $pathInfo = pathinfo($output_image);
            $this->output_image = $pathInfo['dirname'] . "/" . $pathInfo['filename'];
        } else {
            if (!isset($this->source_image) or !file_exists($this->source_image)) {
                throw new Exception("Output Image Path Not Provided.");
            }
            $this->output_image = dirname($this->source_image) . "/Resized Images/" . pathinfo($this->source_image)['filename'];
        }
        return true;
    }

    /**
     * ### Change aspect ratio
     *
     * @param integer $width
     * @param integer $height
     * @return bool
     */

    public function setAspectRatio(int $width, int $height)
    {
        if (is_numeric($width) && is_numeric($height)) {
            $this->aspect_ratio = [$width, $height];
            $this->ratio = $width / $height;
            return true;
        }
        throw new Exception("The width and height should be integer or double." . gettype($ratio) . "' type given.");
    }

    /**
     * ### Return valid value.
     *
     * @param integer $number
     * @param integer $minAllow
     * @param integer $maxAllow
     * @return int
     */
    private function getVal(int $number, int $minAllow, int $maxAllow)
    {
        if ($number < $minAllow) {
            $number = $minAllow;
        } elseif ($number > $maxAllow) {
            $number = $maxAllow;
        }
        return $number;
    }

    /**
     * ### Change Image Transparency (Only for PNG and GIF image)
     *
     * @param integer $transparent_level - integer value [0 to 127]
     * @return bool true
     */

    public function setTransparentLevel(int $transparent_level)
    {
        $this->transparent_level = $this->getVal($transparent_level, 0, 127);
        return true;
    }

    /**
     * ### Change Image Compression Level (Only for PNG and GIF image)
     *
     * @param integer $compression_level - integer value [0 to 9]
     * @return bool true
     */

    public function setCompressionLevel(int $compression_level)
    {
        $this->compression_level = $this->getVal($compression_level, 0, 9);
        return true;
    }

    /**
     * ### Change Output Image Quality (Only for JPG image)
     *
     * @param integer $image_quality - integer value [0 to 100]
     * @return bool true
     */

    public function setImageQuality(int $image_quality)
    {
        $this->image_quality = $this->getVal($image_quality, 0, 100);
        return true;
    }

    /**
     * ### Change Background Color (RGB Color Model.)
     *
     * @param integer $R - integer value [0 to 255]
     * @param integer $G - integer value [0 to 255]
     * @param integer $B - integer value [0 to 255]
     * @return bool true
     */

    public function setBackgroundColor(int $R = 0, int $G = 0, int $B = 0)
    {
        $this->background_color = [
            "R" => $R,
            "G" => $G,
            "B" => $B,
        ];
        return true;
    }

    /**
     * ### Return Final Image Size in The Aspect Ratio
     *
     * @param integer $image_width
     * @param integer $image_height
     * @return void
     */

    private function getFinalSize(int $image_width, int $image_height)
    {
        $image_ratio = $image_width / $image_height;
        if ($image_ratio > $this->ratio) {
            $final_width = $image_width;
            $final_height = ($final_width * $this->aspect_ratio[1]) / $this->aspect_ratio[0];
        } elseif ($image_ratio < $this->ratio) {
            $final_height = $image_height;
            $final_width = ($final_height * $this->aspect_ratio[0]) / $this->aspect_ratio[1];
        } else {
            $final_width = $image_width;
            $final_height = $image_height;
        }
        return [
            $final_width,
            $final_height,
        ];
    }

    private function imageInfo()
    {
        $image_type = mime_content_type($this->source_image);
        if (in_array($image_type, $this->valid_image_types_and_mime)) {
            return getimagesize($this->source_image);
        }
        throw new Exception("Unknown Source Image Format");
    }

    /**
     * Save The Image
     *
     * @param string $path
     * @param string $ext
     * @param string $func
     * @return bool
     */
    private function saveImage(string $path, string $ext, string $func)
    {
        if (is_null($path) or trim($path) == '') {
            if (isset($this->output_image)) {
                $path = $this->output_image . $ext;
            } else {
                $path = dirname($this->source_image) . "/Resized Images/" . pathinfo($this->source_image)['filename'] . $ext;
            }
        }

        if (!file_exists(dirname($path)) and !mkdir(dirname($path), 0777, true)) {
            throw new Exception("Unable to create directory " . dirname($path));
        }

        if (file_put_contents($path, $this->$func())) {
            return true;
        }
        return false;
    }

    /**
     * Return PNG Image String
     *
     * @return string
     */

    public function getPNG()
    {
        list($image_width, $image_height) = $this->imageInfo();
        list($new_width, $new_height) = $this->getFinalSize($image_width, $image_height);

        $background_image = imagecreatetruecolor($new_width, $new_height);
        $background = imagecolorallocatealpha($background_image, $this->background_color['R'], $this->background_color['G'], $this->background_color['B'], $this->transparent_level);
        imagefilledrectangle($background_image, 0, 0, $new_width, $new_height, $background);
        ob_start();
        imagepng($background_image, null, $this->compression_level);
        $background_mask_image = ob_get_clean();
        $image1 = imagecreatefromstring($background_mask_image);
        $image2 = imagecreatefromstring($this->source_image_data);
        imagecopy($image1, $image2, (imagesx($image1) / 2) - (imagesx($image2) / 2), (imagesy($image1) / 2) - (imagesy($image2) / 2), 0, 0, imagesx($image2), imagesy($image2));
        ob_start();
        imagepng($image1, null, $this->compression_level);
        $pngImage = ob_get_clean();
        imagedestroy($background_image);
        imagedestroy($image1);
        imagedestroy($image2);
        return $pngImage;
    }

    /**
     * Return Inline PNG Image
     *
     * @return string
     */

    public function getInlinePNG()
    {
        return "data:image/png;base64," . base64_encode($this->getPNG());
    }

    /**
     * Save PNG Image
     *
     * @param string $path
     * @return bool
     */

    public function savePNG(string $path = '')
    {
        return $this->saveImage($path, '.png', 'getPNG');
    }

    /**
     * Return JPG Image String
     *
     * @return string
     */

    public function getJPG()
    {
        list($image_width, $image_height) = $this->imageInfo();
        list($new_width, $new_height) = $this->getFinalSize($image_width, $image_height);

        $background_image = imagecreatetruecolor($new_width, $new_height);
        $background = imagecolorallocate($background_image, $this->background_color['R'], $this->background_color['G'], $this->background_color['B']);
        imagefilledrectangle($background_image, 0, 0, $new_width, $new_height, $background);
        ob_start();
        imagejpeg($background_image, null, $this->image_quality);
        $background_mask_image = ob_get_clean();
        $image1 = imagecreatefromstring($background_mask_image);
        $image2 = imagecreatefromstring($this->source_image_data);
        imagecopy($image1, $image2, (imagesx($image1) / 2) - (imagesx($image2) / 2), (imagesy($image1) / 2) - (imagesy($image2) / 2), 0, 0, imagesx($image2), imagesy($image2));
        ob_start();
        imagejpeg($image1, null, $this->image_quality);
        $jpgImage = ob_get_clean();
        imagedestroy($background_image);
        imagedestroy($image1);
        imagedestroy($image2);
        return $jpgImage;
    }

    /**
     * Return Inline JPG Image
     *
     * @return string
     */

    public function getInlineJPG()
    {
        return "data:image/jpeg;base64," . base64_encode($this->getJPG());
    }

    /**
     * Save JPG Image
     *
     * @param string $path
     * @return bool
     */

    public function saveJPG(string $path = '')
    {
        return $this->saveImage($path, '.jpg', 'getJPG');
    }

    /**
     * Return GIF Image String
     *
     * @return string
     */

    public function getGIF()
    {
        list($image_width, $image_height) = $this->imageInfo();
        list($new_width, $new_height) = $this->getFinalSize($image_width, $image_height);

        $background_image = imagecreatetruecolor($new_width, $new_height);
        $background = imagecolorallocatealpha($background_image, $this->background_color['R'], $this->background_color['G'], $this->background_color['B'], $this->transparent_level);
        imagefilledrectangle($background_image, 0, 0, $new_width, $new_height, $background);
        ob_start();
        // imagegif($background_image);
        imagepng($background_image);
        $background_mask_image = ob_get_clean();
        $image1 = imagecreatefromstring($background_mask_image);
        imagecolortransparent($image1, $background);
        $image2 = imagecreatefromstring($this->source_image_data);
        imagecopy($image1, $image2, (imagesx($image1) / 2) - (imagesx($image2) / 2), (imagesy($image1) / 2) - (imagesy($image2) / 2), 0, 0, imagesx($image2), imagesy($image2));
        ob_start();
        imagegif($image1);
        $gifImage = ob_get_clean();
        imagedestroy($background_image);
        imagedestroy($image1);
        imagedestroy($image2);
        return $gifImage;
    }

    /**
     * Return Inline GIF Image
     *
     * @return string
     */

    public function getInlineGIF()
    {
        return "data:image/gif;base64," . base64_encode($this->getGIF());
    }

    /**
     * Save GIF Image
     *
     * @param string $path
     * @return bool
     */

    public function saveGIF(string $path = '')
    {
        return $this->saveImage($path, '.gif', 'getGIF');
    }
}
