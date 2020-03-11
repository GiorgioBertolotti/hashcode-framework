<?php

use Utils\FileManager;
use Utils\Log;

require_once '../../bootstrap.php';

// classes

class TestCase
{
  public $boardSize;
  public $multiplier;
  public $maxPerMag;
  public $wastedRice = 0;

  public function __construct($baseRice, $boardSize, $grainsPerBag)
  {
    $this->multiplier = $baseRice;
    $this->boardSize = $boardSize;
    $this->maxPerMag = $grainsPerBag;
  }
}

// reader

$fileManager = new FileManager($fileName);
$fileContent = $fileManager->get();

$fileRows = explode("\n", $fileContent);
list($numTestCases) = explode(' ', $fileRows[0]);

$testCases = [];

for ($i = 0; $i < $numTestCases; $i++) {
  list($baseRice, $boardSize, $grainsPerBag) = explode(' ', $fileRows[1 + $i]);
  array_push($testCases, new TestCase($baseRice, $boardSize, $grainsPerBag));
}

Log::out("Finished input reading", 0);
