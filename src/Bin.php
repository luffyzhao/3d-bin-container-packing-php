<?php
declare(strict_types=1);

namespace BinContainerPacking;

use BinContainerPacking\Handlers\IntersectionHandler;
use BinContainerPacking\Types\AxisType;
use BinContainerPacking\Types\PositionType;
use BinContainerPacking\Types\RotationCombinationType;

/**
 * A class representative of a single bin to put @see Item into.
 */
final class Bin implements \JsonSerializable
{
    /**
     * @var mixed The bin's id.
     */
    private $id;

    /**
     * @var float The bin's length.
     */
    private float $length;

    /**
     * @var float The bin's breadth.
     */
    private float $breadth;

    /**
     * @var float The bin's height.
     */
    private float $height;

    /**
     * @var float The bin's volume.
     */
    private float $volume;

    /**
     * @var float The bin's weight.
     */
    private float $weight;

    /**
     * @var iterable The fitted item(s) inside the bin.
     */
    private iterable $fittedItems;

    /**
     * @var float The total fitted bin's volume.
     */
    private float $totalFittedVolume;

    /**
     * @var float The total fitted bin's weight.
     */
    private float $totalFittedWeight;


    /**
     * @param mixed $id The identifier of the bin.
     * @param float $length The length of the bin.
     * @param float $height The height of the bin.
     * @param float $breadth The breadth of the bin.
     * @param float $weight The weight of the bin.
     */
    public function __construct($id, float $length, float $height, float $breadth, float $weight)
    {
        $this->id = $id;

        $this->length = $length;
        $this->height = $height;
        $this->breadth = $breadth;
        $this->volume = (float)$this->length * $this->height * $this->breadth;
        $this->weight = $weight;

        $this->fittedItems = [];
        $this->totalFittedVolume = 0;
        $this->totalFittedWeight = 0;
    }

    /**
     * The bin's id getter.
     *
     * @return mixed The bin's id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * The bin's length getter.
     *
     * @return float The bin's length.
     */
    public function getLength(): float
    {
        return $this->length;
    }

    /**
     * The bin's height getter.
     *
     * @return float The bin's height.
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * The bin's breadth getter.
     *
     * @return float The bin's breadth.
     */
    public function getBreadth(): float
    {
        return $this->breadth;
    }

    /**
     * Get the bin's volume.
     *
     * @return float The bin's volume.
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * The bin's weight getter.
     *
     * @return float The bin's weight.
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * The bin's fitted items getter.
     *
     * @return iterable The bin's fitted items.
     */
    public function getFittedItems(): iterable
    {
        return $this->fittedItems;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterableFittedItems(): \ArrayIterator
    {
        return new \ArrayIterator($this->fittedItems);
    }

    /**
     * Get the bin's total fitted volume.
     *
     * @return float The fitted bin's volume.
     */
    public function getTotalFittedVolume(): float
    {
        return $this->totalFittedVolume;
    }

    /**
     * @param Item $item
     * @return void
     */
    private function setFittedItems(Item $item): void
    {
        if (!$item instanceof Item) {
            throw new \UnexpectedValueException("Item should be an instance of Item class.");
        }

        $this->fittedItems[] = $item;
        $this->totalFittedVolume += $item->getVolume();
        $this->totalFittedWeight += $item->getWeight();
    }

    /**
     * @param array $items
     * @return bool
     */
    public function putItems(array $items)
    {
        foreach ($items as $item){
            if(!$this->putItem($item)){
                return false;
            }
        }
        return true;
    }

    /**
     * 装箱
     * @param Item $item
     * @return bool
     */
    public function putItem(Item $item)
    {
        $fitted = false;
        $fittedItems = $this->getFittedItems();
        if (count($fittedItems) === 0) {
            if ($this->putItemPosition($item, PositionType::START_POSITION)) {
                $fitted = true;
            }
        } else {
            foreach (AxisType::ALL_AXIS as $axis) {

                foreach ($fittedItems as $fittedItem) {
                    $pivot = PositionType::START_POSITION;
                    $dimension = $fittedItem->getDimension();

                    if ($axis === AxisType::LENGTH) {
                        $pivot = [
                            AxisType::LENGTH => $fittedItem->getPosition()[AxisType::LENGTH] + $dimension[AxisType::LENGTH],
                            AxisType::HEIGHT => $fittedItem->getPosition()[AxisType::HEIGHT],
                            AxisType::BREADTH => $fittedItem->getPosition()[AxisType::BREADTH]
                        ];
                    } elseif ($axis === AxisType::HEIGHT) {
                        $pivot = [
                            AxisType::LENGTH => $fittedItem->getPosition()[AxisType::LENGTH],
                            AxisType::HEIGHT => $fittedItem->getPosition()[AxisType::HEIGHT] + $dimension[AxisType::HEIGHT],
                            AxisType::BREADTH => $fittedItem->getPosition()[AxisType::BREADTH]
                        ];
                    } elseif ($axis === AxisType::BREADTH) {
                        $pivot = [
                            AxisType::LENGTH => $fittedItem->getPosition()[AxisType::LENGTH],
                            AxisType::HEIGHT => $fittedItem->getPosition()[AxisType::HEIGHT],
                            AxisType::BREADTH => $fittedItem->getPosition()[AxisType::BREADTH] + $dimension[AxisType::BREADTH]
                        ];
                    }


                    if ($this->putItemPosition($item, $pivot)) {
                        $fitted = true;

                        break;
                    }
                }


                if ($fitted) {
                    break;
                }
            }
        }
        return $fitted;
    }

    /**
     * 按位置装箱
     *
     * @param Item $item The item to put into.
     * @param array $position The starting position.
     *
     * @return bool The flag indicates whether the item can fit into the bin or not,
     * return true if the item can fit into the bin, otherwise false.
     */
    private function putItemPosition(Item $item, array $position): bool
    {
        $fit = false;
        $validItemPosition = $item->getPosition();
        $item->setPosition($position);

        foreach (RotationCombinationType::ALL_ROTATION_COMBINATION as $rotationType) {
            $item->setRotationType($rotationType);
            $dimension = $item->getDimension();

            if (
                $this->length < $position[AxisType::LENGTH] + $dimension[AxisType::LENGTH] ||
                $this->height < $position[AxisType::HEIGHT] + $dimension[AxisType::HEIGHT] ||
                $this->breadth < $position[AxisType::BREADTH] + $dimension[AxisType::BREADTH]
            ) {
                continue;
            }

            $fit = true;

            foreach ($this->fittedItems as $fitted_item) {
                if (IntersectionHandler::isIntersected($fitted_item, $item)) {
                    $fit = false;

                    break;
                }
            }

            if ($fit) {
                if (($this->totalFittedWeight + $item->getWeight()) > $this->weight) {
                    return false;
                }

                $this->setFittedItems($item);
            }

            if (!$fit) {
                $item->setPosition($validItemPosition);
            }

            return $fit;
        }

        if (!$fit) {
            $item->setPosition($validItemPosition);
        }

        return $fit;
    }

    /**
     * The json serialize method.
     *
     * @return array The resulted object.
     */
    public function jsonSerialize(): array
    {
        $vars = get_object_vars($this);

        return $vars;
    }
}