<?php

use Utils\FileManager;
use Utils\Log;

require_once '../../bootstrap.php';

// classes

// reader

$fileManager = new FileManager($fileName);
$fileContent = $fileManager->get();

$fileRows = explode("\n", $fileContent);
$coordinates =  explode(' ', trim($fileRows[0]));

$startingPoints = [$coordinates[0], $coordinates[1]];
$endingPoints = [$coordinates[2], $coordinates[3]];

$numOstacoli = (int) $fileRows[1];

$ostacoli = [];

$maxRow = max($coordinates[0], $coordinates[2]);
$minRow = min($coordinates[0], $coordinates[2]);
$maxCol = max($coordinates[1], $coordinates[3]);
$minCol = min($coordinates[1], $coordinates[3]);

$rowId = 2;
for($i = 0; $i < $numOstacoli; $i++)
{
    $coordinatesOstacolo = $fileRows[$rowId+$i];
    list($primaCo, $secCo, $terzCo, $quartaCo, $quintaCo, $sestaCo) = explode(' ', trim($coordinatesOstacolo));

    $maxCol = max([$maxCol, $primaCo, $terzCo, $quintaCo]);
    $minCol = min([$minCol, $primaCo, $terzCo, $quintaCo]);


    $maxRow = max([$maxRow, $secCo, $quartaCo, $sestaCo]);
    $minRow = min([$minRow, $secCo, $quartaCo, $sestaCo]);

    $ostacoli[] = [
        [$primaCo, $secCo],
        [$terzCo, $quartaCo],
        [$quintaCo, $sestaCo]
    ];
}


Log::out("Finished input reading", 0);
