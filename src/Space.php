<?php

namespace BinContainerPacking;

use Luffyzhao\BinContainerPacking\Dimension;

class Space extends Dimension
{
    private ?Space $parent;
    private ?Space $remainder;

    private int $x; // width
    private int $y; // depth
    private int $z; // height

    public function __construct(?Space $parent, string $name, int $width, int $depth, int $height, int $x, int $y, int $z)
    {
        parent::__construct($name, $width, $depth, $height);
        $this->parent = $parent;
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @param int $x
     */
    public function setX(int $x): void
    {
        $this->x = $x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param int $y
     */
    public function setY(int $y): void
    {
        $this->y = $y;
    }

    /**
     * @return int
     */
    public function getZ(): int
    {
        return $this->z;
    }

    /**
     * @param int $z
     */
    public function setZ(int $z): void
    {
        $this->z = $z;
    }

    /**
     * @return Space
     */
    public function getRemainder(): ?Space
    {
        return $this->remainder;
    }

    /**
     * @param Space|null $remainder
     */
    public function setRemainder(?Space $remainder): void
    {
        $this->remainder = $remainder;
    }

    /**
     * @return Space|null
     */
    public function getParent(): ?Space
    {
        return $this->parent;
    }

    /**
     * @param Space|null $parent
     */
    public function setParent(?Space $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @param int $start
     * @param int $value
     * @param int $end
     * @return bool
     */
    protected static function between(int $start, int $value, int $end): bool
    {
        return $start <= $value && $value <= $end;
    }

    /**
     * @param int $start
     * @param int $end
     * @param int $value
     * @param int $distance
     * @return bool
     */
    protected function intersects(int $start, int $end, int $value, int $distance): bool
    {
        return self::between($start, $value, $end) || self::between($start, $value - $distance, $end) || ($value < $start && $end < $value + $distance);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "Space [name=" . $this->name . ", " . $this->x . "x" . $this->y . "x" . $this->z . ", width=" . $this->width . ", depth=" . $this->depth . ", height="
            . $this->height . "]";
    }

    /**
     * @return int
     */
    public function hashCode(): int
    {
        $prime = 31;
        $result = parent::hashCode();
        $result = $prime * $result + (($this->parent === null) ? 0 : $this->parent->hashCode());
        $result = $prime * $result + (($this->remainder === null) ? 0 : $this->remainder->hashCode());
        $result = $prime * $result + $this->x;
        $result = $prime * $result + $this->y;
        $result = $prime * $result + $this->z;
        return $result;
    }

    /**
     * @param Space|null $obj
     * @return bool
     */
    public function equals(?Space $obj): bool
    {
        if ($this === $obj) {
            return true;
        }
        if (parent::equals($obj)) {
            return false;
        }
        if (get_class($this) !== get_class($obj)) {
            return false;
        }
        if ($this->parent === null) {
            if ($obj->parent !== null) {
                return false;
            }
        } else if (!$this->parent . $this->equals($obj->parent)) {
            return false;
        }

        if ($this->remainder === null) {
            if ($obj->remainder !== null) {
                return false;
            }
        } else if (!$this->remainder->equals($obj->remainder)) {
            return false;
        }

        if ($this->x !== $obj->x) {
            return false;
        }

        if ($this->y !== $obj->y) {
            return false;
        }

        if ($this->z !== $obj->z) {
            return false;
        }

        return true;


    }

    /**
     * @param Space $space
     * @return void
     */
    public function copyFrom(Space $space)
    {
        $this->parent = $space->parent;
        $this->x = $space->parent;
        $this->y = $space->y;
        $this->z = $space->z;
        $this->width = $space->width;
        $this->height = $space->height;
        $this->depth = $space->depth;
    }

    /**
     * @param Space $space
     * @return bool
     */
    public function intersectsForSpace(Space $space): bool
    {
        return $this->intersectsX($space) && $this->intersectsY($space) && $this->intersectsZ($space);
    }

    /**
     * @param Space $space
     * @return bool
     */
    public function intersectsY(Space $space)
    {
        $startY = $space->getY();
        $endY = $startY + $space->getDepth() - 1;
        return  $this->intersects($startY, $endY, $this->y, $this->depth);
    }

    /**
     * @param int $startY
     * @param int $endY
     * @return bool
     */
    public function intersectsAxisY(int $startY, int $endY): bool
    {
        return $this->intersects($startY, $endY, $this->y, $this->depth);
    }

    /**
     * @param Space $space
     * @return bool
     */
    private function intersectsX(Space $space):bool
    {
        $startX = $space->getX();
        $endX = $startX + $space->getWidth() - 1;
        return $this->intersects($startX, $endX, $this->x, $this->width);
    }

}