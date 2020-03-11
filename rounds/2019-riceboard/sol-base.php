<?php

use Utils\Log;
use Utils\Stopwatch;

$fileName = 'a';

require_once 'reader.php';

// Runtime

$SCORE = 0;

Stopwatch::tik('Totale');

Log::out("SCORE: " . $SCORE, 0);

Stopwatch::tok('Totale');
Stopwatch::print('Totale');

// populate output array
$output = [];
$fileManager->output(implode("\n", $output));
