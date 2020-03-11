<?php

use Utils\Log;
use Utils\Stopwatch;

$fileName = 'a';

require_once 'reader.php';

// Runtime

$SCORE = 0;

Stopwatch::tik('Totale');
$output = [];

for ($case = 0; $case < count($tests); $case++) {
  $test = $tests[$case];
  $numCells = pow($test->boardSize, 2);
  $somma = (pow($test->multiplier, $numCells + 1) - 1) / ($test->multiplier - 1);
  $avanzati = $somma % $test->maxPerBag;
  array_push($output, "Case #" . ($case + 1) . ": " . $avanzati);
}

Log::out("SCORE: " . $SCORE, 0);

Stopwatch::tok('Totale');
Stopwatch::print('Totale');

// populate output array
$fileManager->output(implode("\n", $output));
