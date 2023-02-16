<?php
declare(strict_types=1);
namespace BinContainerPacking\Types;

/**
 * Enum of possible axises.
 */
class AxisType
{
    // Represents the 3d-plane axis
    public const LENGTH = 'x-axis';
    public const HEIGHT = 'y-axis';
    public const BREADTH = 'z-axis';

    // Enum contains all the 3d-plane axis
    public const ALL_AXIS = [
        AxisType::LENGTH,
        AxisType::HEIGHT,
        AxisType::BREADTH
    ];
}