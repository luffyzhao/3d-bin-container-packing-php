<?php

namespace BinContainerPacking;

use InvalidArgumentException;
use Luffyzhao\BinContainerPacking\Dimension;

class Box extends Dimension
{
    /**
     * @return $this
     */
    public function rotate3D(): self
    {
        $height = $this->height;
        $this->height = $this->width;
        $this->width = $this->depth;
        $this->depth = $height;
        return $this;
    }

    /**
     * @param int $w
     * @param int $d
     * @param int $h
     * @return bool
     */
    private function fitsWidthAndDepthDown(int $w, int $d, int $h): bool
    {
        if ($h < $this->height) {
            return false;
        }
        return ($d >= $this->width && $w >= $this->depth) || ($w >= $this->width && $d >= $this->depth);
    }

    /**
     * @param int $w
     * @param int $d
     * @param int $h
     * @return bool
     */
    private function fitsHeightAndDepthDown(int $w, int $d, int $h): bool
    {
        if ($h < $this->width) {
            return false;
        }
        return ($d >= $this->height && $w >= $this->depth) || ($w >= $this->height && $d >= $this->depth);
    }

    /**
     * @param int $w
     * @param int $d
     * @param int $h
     * @return bool
     */
    private function fitsHeightAndWidthDown(int $w, int $d, int $h): bool
    {
        if ($h < $this->depth) {
            return false;
        }
        return ($d >= $this->height && $w >= $this->width) || ($w >= $this->height && $d >= $this->width);
    }

    /**
     * @param int $w
     * @param int $d
     * @param int $h
     * @return bool
     */
    private function rotateLargestFootprint3D(int $w, int $d, int $h): bool
    {
        $a = 0;
        if ($this->fitsWidthAndDepthDown($w, $d, $h)) {
            $a = $this->width * $this->depth;
        }

        $b = 0;
        if ($this->fitsHeightAndDepthDown($w, $d, $h)) {
            $b = $this->height * $this->depth;
        }

        $c = 0;
        if ($this->fitsHeightAndWidthDown($w, $d, $h)) {
            $c = $this->width * $this->height;
        }

        if ($a === 0 && $b === 0 && $c === 0) {
            return false;
        }

        if ($a > $b && $a > $c) {
            // no rotate
        } else if ($b > $c) {
            // rotate once
            $this->rotate3D();
        } else {
            $this->rotate3D();
            $this->rotate3D();
        }

        if ($h < $this->height) {
            throw new InvalidArgumentException("Expected height " . $this->height . " to fit within height constraint " . $h);
        }

        if ($this->width > $w || $this->depth > $d) {
            // use the other orientation
            $this->rotate2D();
        }
        if ($this->width > $w || $this->depth > $d) {
            throw new InvalidArgumentException("Expected width " . $this->width . " and depth " . $this->depth . " to fit within constraint width " . $w . " and depth " . $d);
        }
        return true;
    }

    /**
     * @param int $w
     * @param int $d
     * @return bool
     */
    public function fitRotate2D(int $w, int $d): bool
    {
        if ($w >= $this->width && $d >= $this->depth) {
            return true;
        }
        if ($d >= $this->width && $w >= $this->depth) {
            $this->rotate2D();
            return true;
        }

        return false;
    }

    /**
     * @param int $w
     * @param int $d
     * @param int $h
     * @return bool
     */
    public function fitRotate3DSmallestFootprint(int $w, int $d, int $h): bool
    {
        $a = 0;
        if ($this->fitsWidthAndDepthDown($w, $d, $h)) {
            $a = $this->width * $this->depth;
        }

        $b = 0;
        if ($this->fitsHeightAndDepthDown($w, $d, $h)) {
            $b = $this->height * $this->depth;
        }

        $c = 0;
        if ($this->fitsHeightAndWidthDown($w, $d, $h)) {
            $c = $this->width * $this->height;
        }

        if ($a === 0 && $b === 0 && $c == 0) {
            return false;
        }

        if ($a < $b && $a < $c) {
            // no rotate
        } else if ($b < $c) {
            // rotate once
            $this->rotate3D();
        } else {
            $this->rotate3D();
            $this->rotate3D();
        }

        if ($h < $this->height) {
            throw new InvalidArgumentException("Expected height " . $this->height . " to fit within height constraint " . $h);
        }

        if ($this->width > $w || $this->depth > $d) {
            // use the other orientation
            $this->rotate2D();
        }

        if ($this->width > $w || $this->depth > $d) {
            throw new InvalidArgumentException("Expected width " . $this->width . " and depth " . $this->depth . " to fit within constraint width " . $w . " and depth " . $d);
        }

        return true;
    }

    /**
     * @return float|int
     */
    public function currentSurfaceArea(): int
    {
        return $this->width * $this->height;
    }

    /**
     * @return Box
     */
    public function clone(): Box
    {
        return new Box($this->name, $this->getWidth(), $this->getDepth(), $this->getHeight());
    }

    /**
     * @return $this
     */
    public function rotate2D(): Box
    {
        $depth = $this->getDepth();
        $this->depth = $this->getWidth();
        $this->width = $depth;
        return $this;
    }

    /**
     * @return $this
     */
    public function rotate2D3D(): Box
    {
        $depth = $this->getDepth();
        $this->depth = $this->getHeight();
        $this->height = $depth;
        return $this;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->getWidth();
    }

    public function __toString():string
    {
        return "Box [width=" . $this->width . ", depth=" . $this->depth . ", height=" . $this->height . ", volume="
            . $this->volume . ", name=" . $this->name . ", weight=" . $this->weight . "]";
    }
}