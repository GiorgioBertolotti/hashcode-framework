<?php

use Utils\Log;
use Utils\Stopwatch;

require_once '../../bootstrap.php';

$fileName = 'b';

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

Stopwatch::tik('Tuple');

$all_replayers = array_merge($developers, $managers);
$tuple = [];
for ($i = 0; $i < count($all_replayers); $i++) {
  for ($j = 0; $j < count($all_replayers); $j++) {
    if ($i == $j)
      continue;
    $repl1 = $all_replayers[$i];
    $repl2 = $all_replayers[$j];
    $score = calculateBonus($repl1, $repl2);

    if ($score > 0) {
      $tuple[] = new Tupla($repl1, $repl2, $score);
    }
  }
  Log::out("Replayers rimanenti: " . (count($all_replayers) - $i), 0);
}

Stopwatch::tok('Tuple');
Stopwatch::print('Tuple');
