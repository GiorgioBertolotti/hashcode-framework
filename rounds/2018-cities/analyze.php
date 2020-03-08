<?php

$fileName = 'b';

include 'reader.php';
include_once '../../utils/Analysis/Analyzer.php';

$analyzer = new Analyzer($fileName, [
    'residence_count' => count($buildings->where('buildingType', 'R')),
    'utility_count' => count($buildings->where('buildingType', 'U')),
]);

$analyzer->analyze();
/*
$analyzer->addDataset('books', $books, ['award', 'inLibraries']);
$analyzer->addDataset('libraries', $libraries, ['signUpDuration', 'shipsPerDay', 'books']);
$analyzer->analyze();
*/