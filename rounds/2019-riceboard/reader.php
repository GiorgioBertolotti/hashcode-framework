<?php

use Utils\FileManager;
use Utils\Log;

require_once '../../bootstrap.php';

// classes

class TestCase
{
  private $boardSize;
  private $baseRice;
  private $grainsPerBag;

  public function __construct($baseRice, $boardSize, $grainsPerBag)
  {
    $this->baseRice = $baseRice;
    $this->boardSize = $boardSize;
    $this->grainsPerBag = $grainsPerBag;
  }
}

// reader

$fileManager = new FileManager($fileName);
$fileContent = $fileManager->get();

$fileRows = explode("\n", $fileContent);
list($numTestCases) = explode(' ', $fileRows[0]);

print_r($numTestCases);

$testCases = [];

for ($i = 0; $i < $numTestCases; $i++) {
  list($baseRice, $boardSize, $grainsPerBag) = explode(' ', $fileRows[1 + $i]);
  array_push($testCases, new TestCase($baseRice, $boardSize, $grainsPerBag));
}

print_r($testCases);
Log::out("Finished input reading", 0);
