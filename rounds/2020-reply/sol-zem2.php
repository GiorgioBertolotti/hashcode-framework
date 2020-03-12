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

//for($r = 0; $r < count($))

Stopwatch::tok('Tuple');
Stopwatch::print('Tuple');
