<?php

use Utils\FileManager;
use Utils\Log;

require_once '../../bootstrap.php';

// classes

class Test
{
  public $boardSize;
  public $multiplier;
  public $maxPerBag;
  public $wastedRice = 0;

  public function __construct($baseRice, $boardSize, $grainsPerBag)
  {
    $this->multiplier = $baseRice;
    $this->boardSize = $boardSize;
    $this->maxPerBag = $grainsPerBag;
  }
}

// reader

$fileManager = new FileManager($fileName);
$fileContent = $fileManager->get();

$fileRows = explode("\n", $fileContent);
list($numTests) = explode(' ', $fileRows[0]);

$tests = [];

for ($i = 0; $i < $numTests; $i++) {
  list($baseRice, $boardSize, $grainsPerBag) = explode(' ', $fileRows[1 + $i]);
  array_push($tests, new Test($baseRice, $boardSize, $grainsPerBag));
}

Log::out("Finished input reading", 0);
