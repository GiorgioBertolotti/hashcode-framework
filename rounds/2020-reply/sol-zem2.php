<?php

use Utils\Log;
use Utils\Stopwatch;

require_once '../../bootstrap.php';

$fileName = 'a';

include 'reader.php';

class Tupla
{
  public $repl1;
  public $repl2;
  public $score;

  public function __construct($repl1, $repl2, $score)
  {
    $this->repl1 = $repl1;
    $this->repl2 = $repl2;
    $this->score = $score;
  }
}

Stopwatch::tik('Mappa');

for ($r = 0; $r < count($office); $r++) {
  $row = $office[$r];
  for ($c = 0; $c < count($row); $c++) {
    $cell = $row[$c];
    if ($cell == '#')
      continue;
    if ($c != count($row) - 1) {
      $rightSit = $office[$r][$c + 1];
      if ($rightSit != '#') {
        // c'è un posto a destra
        Log::out($cell . " ha un posto a destra: " . $rightSit, 0);
      }
    }
    if ($r != count($office) - 1) {
      $downSit = $office[$r + 1][$c];
      if ($downSit != '#') {
        // c'è un posto sotto
        Log::out($cell . " ha un posto sotto: " . $downSit, 0);
      }
    }
  }
}

Stopwatch::tok('Mappa');
Stopwatch::print('Mappa');
