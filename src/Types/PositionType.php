<?php
declare(strict_types=1);
namespace BinContainerPacking\Types;

/**
 * Enum of possible positions.
 */
class PositionType
{
    // Start position
    public const START_POSITION = [
        AxisType::LENGTH    => 0,
        AxisType::HEIGHT    => 0,
        AxisType::BREADTH   => 0
    ];
}