<?php

use Utils\Log;
use Utils\Stopwatch;

$fileName = 'e';

require_once 'reader.php';

// Runtime

$SCORE = 0;

Stopwatch::tik('Totale');
$output = [];

for ($case = 0; $case < count($tests); $case++) {
  $test = $tests[$case];
  $numCells = pow($test->boardSize, 2);

  $rest = 1;
  $lastPow = 1;
  for ($i = 1; $i < $numCells; $i++) {
    $lastPow = $test->multiplier * $lastPow;
    $rest += $lastPow;
    $rest = $rest % $test->maxPerBag;
  }
  Log::out("rest: " . $rest, 0);

  $somma = (pow($test->multiplier, $numCells) - 1) / ($test->multiplier - 1);
  $avanzati = $somma % $test->maxPerBag;
  Log::out("avanzati: " . $avanzati, 0);
  array_push($output, "Case #" . ($case + 1) . ": " . $rest);
}

Log::out("SCORE: " . $SCORE, 0);

Stopwatch::tok('Totale');
Stopwatch::print('Totale');

// populate output array
$fileManager->output(implode("\n", $output));
