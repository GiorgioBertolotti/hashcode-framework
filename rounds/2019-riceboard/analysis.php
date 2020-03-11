<?php

$fileName = 'a';

include 'reader.php';
include_once '../../utils/Analysis/Analyzer.php';

// Algo

$analyzer = new Analyzer($fileName, [
  // 'numBooks' => count($numBooks),
  // 'numLibraries' => count($numLibraries),
  // 'numDays' => $numDays,
]);
//$analyzer->addDataset('books', $books, ['score']);
$analyzer->analyze();
