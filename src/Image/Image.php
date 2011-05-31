<?php
/*
 * ========================================================================
 * Copyright (c) 2011 Vladislav "FractalizeR" Rastrusny
 * Website: http://www.fractalizer.ru
 * Email: FractalizeR@yandex.ru
 * ------------------------------------------------------------------------
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================================
 */

namespace phpImageBatch\Image;

/**
 * Class for handling various image operations
 *
 * @throws ImageException
 */

class Image {

    /**
     * The type of the opened image
     * @var int
     */
    private $_imageType;

    /**
     * GD handle of the opened image
     * @var \resource
     */
    private $_image;

    static function createFromFile($filename) {
        if (!file_exists($filename)) {
            throw new ImageException(sprintf('File "%s" does not exist!', $filename));
        }

        $imageInfo = getimagesize($filename);
        $imageType = $imageInfo[2];
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($filename);
                break;
            case IMAGETYPE_WBMP:
                $image = imagecreatefromwbmp($filename);
                break;
            case IMAGETYPE_XBM:
                $image = imagecreatefromxbm($filename);
                break;
            default:
                throw new ImageException(sprintf('Unknown image type in file "%s"', $filename));
                break;
        }
        if ($image === false) {
            throw new ImageException(sprintf('Cannot open image with filename "%s"', $image));
        }
        return new Image($image, $imageType);
    }

    static function createFromString($image, $imageType = IMAGETYPE_JPEG) {
        $image = imagecreatefromstring($image);
        if ($image === false) {
            throw new ImageException('Parameter $image is not a string representation of image!');
        }

        return new Image($image, $imageType);
    }

    /**
     * Creates image object from GD resource
     *
     * @see createFromString
     * @see createFromFile
     *
     * @param \resource $image From which to create an image
     * @param int $imageType The type of the image (will be used on saving operations by default)
     * @return \phpImageBatch\Image\Image
     */
    function __construct($image, $imageType = IMAGETYPE_JPEG) {
        if (!is_resource($image)) {
            throw new ImageException('Parameter $image is not a resource!');
        }
        $this->_image = $image;
        $this->_imageType = $imageType;
    }

    /**
     * Function to save or display an image
     *
     * @throws Exception
     * @param string $filename Filename to save file into. If null - image will be directly sent to browser instead
     * @param int $imageType The type if the image to save into. Valid types are denoted by GD constants IMAGETYPE_X.
     * If null, source image type will be taken, from which it was loaded
     * @param int $compression Compression ratio for JPEG images. 75 by default.
     * @return void
     */
    function saveToFile($filename = null, $imageType = null, $compression = 75) {
        if (is_null($imageType)) {
            $imageType = $this->_imageType;
        }

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($this->_image, $filename, $compression);
                break;
            case IMAGETYPE_GIF:
                $result = imagegif($this->_image, $filename);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($this->_image, $filename);
                break;
            case IMAGETYPE_WBMP:
                $result = imagewbmp($this->_image, $filename);
                break;
            case IMAGETYPE_XBM:
                $result = imagexbm($this->_image, $filename);
                break;
            default:
                throw new ImageException('Unknown image type to save to!');
                break;
        }

        if (!$result) {
            throw new ImageException(sprintf('Cannot save image to filename "%s" with imagetype "%s" and compression %s',
                    $filename, $imageType, $compression));
        }

    }

    /**
     * Returns the width of the image
     *
     * @return int
     */
    function getWidth() {
        return imagesx($this->_image);
    }

    /**
     * Returns the height of the image
     *
     * @return int
     */
    function getHeight() {
        return imagesy($this->_image);
    }

    /**
     * Returns a copy of the current image proportionally resized to a given height
     *
     * @param int $height
     * @return \phpImageBatch\Image\Image
     */
    function resizeToHeight($height) {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        return $this->resizeTo($width, $height);
    }

    /**
     * Returns a copy of the current image proportionally resized to a given width
     *
     * @param int $width
     * @return \phpImageBatch\Image\Image
     */
    function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getHeight() * $ratio;
        return $this->resizeTo($width, $height);
    }

    /**
     *  Returns a copy of the current image scaled by a given factor
     *
     * @param int $scale Scale factor in percents
     * @return \phpImageBatch\Image\Image
     */
    function scale($scale) {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getHeight() * $scale / 100;
        return $this->resizeTo($width, $height);
    }

    /**
     *  Returns a copy of the current image resized to a given size preserving alpha state of the image
     *
     * @param int $width
     * @param int $height
     * @return \phpImageBatch\Image\Image
     */
    function resizeTo($width, $height) {
        $newImage = imagecreatetruecolor($width, $height);
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $width, $height, $transparent);

        if (!imagecopyresampled($newImage, $this->_image, 0, 0, 0, 0, $width, $height, $this->getWidth(),
            $this->getHeight())
        ) {
            throw new ImageException(sprintf('Cannot resize image to width "%d" and height "%d"!', $width, $height));
        }
        return new Image($newImage);
    }

    /**
     * @return \resource GD handle of the containing image
     */
    function getResource() {
        return $this->_image;
    }

    /**
     * Copies this image into another one
     *
     * @throws ImageOperationException
     * @param Image $image Image to copy to
     * @param int $x Coordinate on X axis where to copy this image to
     * @param int $y Coordinate on Y axis where to copy this image to
     */
    function copyTo(Image $image, $x, $y) {
        if (!imagecopy($image->getResource(), $this->getResource(), $x, $y, 0, 0, $this->getWidth(), $this->getHeight())
        ) {
            throw new ImageException('Cannot merge image!');
        }
    }
}
