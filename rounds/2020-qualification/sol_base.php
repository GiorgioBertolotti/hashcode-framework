<?php

use Utils\Stopwatch;

$fileName = 'a';

require_once 'reader.php';

/* functions */

Stopwatch::tik('Totale');

/* runtime */
// mamma
Stopwatch::tok('Totale');
Stopwatch::print('Totale');
