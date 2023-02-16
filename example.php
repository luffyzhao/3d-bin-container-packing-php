<?php

declare(strict_types=1);

use BinContainerPacking\Bin;
use BinContainerPacking\Item;

require_once __DIR__ . '/vendor/autoload.php';


$bin = new Bin(11, 10, 10, 10, 5);

$items = [
    new Item(1, 4, 4.1, 4.1, 50),
    new Item(13, 6, 6, 6, 5),
];

if (!$bin->putItems($items)) {
    print_r("装不下这么多！\n");
}