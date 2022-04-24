<?php

namespace Luffyzhao\BinContainerPacking;

class Dimension
{
    protected int $width; // x
    protected int $depth; // y
    protected int $height; // z
    protected double $volume;

    protected string $name;

    /**
     * @param string $name
     * @param int $width
     * @param int $depth
     * @param int $height
     */
    public function __construct(string $name, int $width, int $depth, int $height)
    {
        $this->name = $name;
        $this->width = $width;
        $this->depth = $depth;
        $this->height = $height;

        $this->calculateVolume();
    }

    protected function calculateVolume(): void
    {

        $this->volume = ((double)$this->depth) * ((double)$this->width) * ((double)$this->height);
    }

    /**
     * @param int $width
     * @param int $depth
     * @param int $height
     * @return bool
     */
    public function canHold3D(int $width, int $depth, int $height): bool
    {
        return ($width <= $this->width && $height <= $this->height && $depth <= $this->depth) ||
            ($height <= $this->width && $depth <= $this->height && $width <= $this->depth) ||
            ($depth <= $this->width && $width <= $this->height && $height <= $this->depth) ||
            ($height <= $this->width && $width <= $this->height && $depth <= $this->depth) ||
            ($depth <= $this->width && $height <= $this->height && $width <= $this->depth) ||
            ($width <= $this->width && $depth <= $this->height && $height <= $this->depth);
    }

    /**
     * @return bool
     */
    public function nonEmpty(): bool
    {
        return $this->width > 0 && $this->depth > 0 && $this->height > 0;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "Dimension [width=" + $this->width + ", depth=" + $this->depth + ", height=" + $this->height + ", volume=" + $this->volume + "]";
    }

    /**
     * @return int|string
     */
    public function __encode()
    {
        return static::encode($this->width, $this->depth, $this->height);
    }

    /**
     * @param $str
     * @return string
     */
    public function stringHashCode($str)
    {
        if (empty($str))
            return '';
        $str = strtoupper($str);
        $mdv = md5($str);
        $mdv1 = substr($mdv, 0, 16);
        $mdv2 = substr($mdv, 16, 16);
        $crc1 = abs(crc32($mdv1));
        $crc2 = abs(crc32($mdv2));
        return bcmul($crc1, $crc2);
    }

    /**
     * @return int
     */
    public function hashCode(): int
    {
        $prime = 31;
        $result = 1;
        $result = $prime * $result + $this->depth;
        $result = $prime * $result + $this->height;
        $result = $prime * $result + (($this->name == null) ? 0 : $this->stringHashCode($this->name));
        $result = $prime * $result + (int)$this->volume;
        $result = $prime * $result + $this->width;
        return $result;
    }

    /**
     * @param int $w
     * @param int $d
     * @param int $h
     * @return bool
     */
    public function canHold2D(int $w, int $d, int $h): bool
    {
        if ($h > $this->height) {
            return false;
        }
        return ($w <= $this->width && $d <= $this->depth) || ($d <= $this->width && $w <= $this->depth);
    }

    /**
     * @return int
     */
    public function getFootprint(): int
    {
        return $this->width * $this->depth;
    }

    /**
     * @return bool
     */
    public function isSquare2D(): bool
    {
        return $this->width == $this->depth;
    }

    /**
     * @return bool
     */
    public function isSquare3D(): bool
    {
        return $this->width == $this->depth && $this->width == $this->height;
    }

    /**
     * @param $size
     * @return static
     */
    public static function decode($size): self
    {
        $dimensions = explode($size, 'x');

        return self::newInstance((int)$dimensions[0], (int)$dimensions[1], (int)$dimensions[2]);
    }

    /**
     * @param int $w
     * @param int $d
     * @param int $h
     * @return bool
     */
    public function fitsInside3D(int $w, int $d, int $h): bool
    {
        return $w >= $this->width && $h >= $this->height && $d >= $this->depth;
    }

    /**
     * @param int $width
     * @param int $depth
     * @param int $height
     * @return Dimension
     */
    public static function newInstance(int $width, int $depth, int $height): Dimension
    {
        return new Dimension($width, $depth, $height);
    }

    /**
     * @param int $width
     * @param int $depth
     * @param int $height
     * @return int|string
     */
    public static function encode(int $width, int $depth, int $height): string
    {
        return $width . "x" . $depth . "x" . $height;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @param int $depth
     */
    public function setDepth(int $depth): void
    {
        $this->depth = $depth;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
     * @return long
     */
    public function getVolume(): long
    {
        return $this->volume;
    }

    /**
     * @param long $volume
     */
    public function setVolume(long $volume): void
    {
        $this->volume = $volume;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param Dimension|null $obj
     * @return bool
     */
    public function equals(?Dimension $obj)
    {
        if ($this === $obj) {
            return true;
        }

        if ($obj === null) {
            return false;
        }

        if(get_class($this) !== get_class($obj)){
            return false;
        }

        if ($this->depth !== $obj->depth)
            return false;
        if ($this->height != $obj->height)
            return false;
        if ($this->name == null) {
            if ($obj->name != null)
                return false;
        } else if ($this->name !== $obj->name)
            return false;
        if ($this->volume != $obj->volume)
            return false;
        if ($this->width != $obj->width)
            return false;
        return true;
    }

}