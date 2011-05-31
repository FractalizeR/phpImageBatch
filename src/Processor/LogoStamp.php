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

namespace phpImageBatch\Processor;

use phpImageBatch\Image\Image;

class LogoStamp implements Processor {
    /**#@+
     * @var int Controls logo resizing on stamping
     */
    const MODE_RESIZE_NONE = 0;
    const MODE_RESIZE_SCALE = 1;
    const MODE_RESIZE_TO_HEIGHT = 2;
    const MODE_RESIZE_TO_WIDTH = 3;
    /**#@-*/

    /**
     * @var array Values, that are valid for resize operations
     */
    private $validResizeModes =
    array(self::MODE_RESIZE_NONE, self::MODE_RESIZE_TO_HEIGHT, self::MODE_RESIZE_TO_WIDTH, self::MODE_RESIZE_SCALE);

    /**#@+
     * @var int Controls logo placing on stamping
     */
    const PLACE_CENTER = 0;
    const PLACE_LEFT = 1;
    const PLACE_RIGHT = 2;
    const PLACE_TOP = 3;
    const PLACE_BOTTOM = 4;
    /**#@-*/

    /**
     * @var array Values that are valid for horizontal placement operations
     */
    private $validPlacementVertical = array(self::PLACE_CENTER, self::PLACE_TOP, self::PLACE_BOTTOM);

    /**
     * @var array Values that are valid for vertical placement operations
     */
    private $validPlacementHorizontal = array(self::PLACE_CENTER, self::PLACE_LEFT, self::PLACE_RIGHT);

    /**
     * @var Image
     */
    private $logo;

    /**
     * @var int How to resize logotype before stamping
     */
    private $logoResizeMode = self::MODE_RESIZE_TO_WIDTH;

    /**
     * @var float How much of the original picture should logo occupy in range from 0 to 1.
     */
    private $logoScalingRatio = 0.5;

    /**
     * @var int Where to place logo on the target image on Y axis
     */
    private $logoPlacementVertical = self::PLACE_CENTER;

    /**
     * @var int Where to place logo on the target image on X axis
     */
    private $logoPlacementHorizontal = self::PLACE_CENTER;

    /**
     * Construct the stamper and initializes it to use specified logo
     *
     * @param \phpImageBatch\Image\Image $logo The image to use as a logo
     * @return \phpImageBatch\Processor\LogoStamp
     */
    public function __construct(Image $logo) {
        $this->logo = $logo;
    }

    /**
     * Stamps the logo into the given image
     *
     * @throws StampException
     * @param \phpImageBatch\Image\Image $image Image on which to stamp the logo
     * @return void
     */
    public function process(Image $image) {

        //Resizing logo
        switch ($this->logoResizeMode) {
            case self::MODE_RESIZE_NONE:
                $logo = $this->logo;
                break;
            case self::MODE_RESIZE_TO_HEIGHT:
                $logo = $this->logo->resizeToHeight($image->getHeight() * $this->logoScalingRatio);
                break;
            case self::MODE_RESIZE_TO_WIDTH:
                $logo = $this->logo->resizeToWidth($image->getWidth() * $this->logoScalingRatio);
                break;
            case self::MODE_RESIZE_SCALE:
                $logo = $this->logo->scale($this->logoScalingRatio);
                break;
            default:
                throw new ProcessorException("Invalid logoResizeMode!");
                break;
        }

        //Placing logo
        switch ($this->logoPlacementVertical) {
            case self::PLACE_CENTER:
                $y = ($image->getHeight() - $logo->getHeight()) / 2;
                break;
            case self::PLACE_LEFT:
                $y = 0;
                break;
            case self::PLACE_RIGHT:
                $y = $image->getHeight() - $logo->getHeight();
                break;
            default:
                throw new ProcessorException("Unknown vertical placement!");
        }

        switch ($this->logoPlacementHorizontal) {
            case self::PLACE_CENTER:
                $x = ($image->getWidth() - $logo->getWidth()) / 2;
                break;
            case self::PLACE_TOP:
                $x = 0;
                break;
            case self::PLACE_BOTTOM:
                $x = $image->getWidth() - $logo->getWidth();
                break;
            default:
                throw new ProcessorException("Unknown placement!");
        }

        $logo->copyTo($image, $x, $y);
    }

    /**
     * Returns logo scaling ratio
     *
     * @return float
     */
    public function getLogoScalingRatio() {
        return $this->logoScalingRatio;
    }

    /**
     * Sets logo scaling ratio
     *
     * @param float $ratio Logo scaling ratio (0..1), which is used in resizing operations before stamping
     */
    public function setLogoScalingRatio($ratio) {
        if ($ratio < 0 or $ratio > 1) {
            throw new ProcessorException("Invalid scaling ratio. Valid is float from 0 to 1");
        }
        $this->logoScalingRatio = $ratio;
    }

    /**
     * Returns current logo resize mode
     * @see getLogoOpacity
     * @return int
     */
    public function getLogoResizeMode() {
        return $this->logoResizeMode;
    }

    /**
     * Sets the logo resize mode.
     *
     * @param int $mode How to resize logo before stamping.
     * @return void
     * @see MODE_RESIZE_NONE
     * @see MODE_RESIZE_SCALE
     * @see MODE_RESIZE_TO_HEIGHT
     * @see MODE_RESIZE_TO_WIDTH
     */
    public function setLogoResizeMode($mode) {
        if (!in_array($mode, $this->validResizeModes)) {
            throw new ProcessorException("Invalid resize mode. See docs");
        }
        $this->logoResizeMode = $mode;
    }

    /**
     * Returns logo placement on Y axis
     *
     * @see setLogoPlacementVertical
     * @return int
     */
    public function getLogoPlacementVertical() {
        return $this->logoPlacementVertical;
    }

    /**
     * Sets logo placement on stamping on the Y axis
     *
     * @param int $placement Where to place logo on the target image. Valid values are:
     * @see PLACE_CENTER
     * @see PLACE_TOP
     * @see PLACE_BOTTOM
     */
    public function setLogoPlacementVertical($placement) {
        if (!in_array($placement, $this->validPlacementVertical)) {
            throw new ProcessorException("Invalid vertical placement! See docs.");
        }
        $this->logoPlacementVertical = $placement;
    }

    /**
     * Returns logo placement on X axis
     *
     * @see setLogoPlacementHorizontal
     * @return int
     */
    public function getLogoPlacementHorizontal() {
        return $this->logoPlacementHorizontal;
    }

    /**
     * Sets logo placement on stamping on the X axis
     *
     * @param int $placement Where to place logo on the target image. Valid values are:
     * @see PLACE_CENTER
     * @see PLACE_LEFT
     * @see PLACE_RIGHT
     */
    public function setLogoPlacementHorizontal($placement) {
        if (!in_array($placement, $this->validPlacementHorizontal)) {
            throw new ProcessorException("Invalid horizontal placement! See docs.");
        }
        $this->logoPlacementHorizontal = $placement;
    }
}
