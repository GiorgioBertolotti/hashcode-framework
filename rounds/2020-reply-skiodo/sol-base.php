<?php

use Utils\Cerberus;
use Utils\Log;
use Utils\Stopwatch;

require_once '../../bootstrap.php';

// Cerberus

$fileName = null;
$kPow = null;
Cerberus::runClient(['fileName' => 'a', 'kPow' => 1.0]);

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
