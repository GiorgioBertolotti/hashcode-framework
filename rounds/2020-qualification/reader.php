<?php

use Utils\FileManager;
use Utils\Log;

require_once '../../bootstrap.php';

/* classes */

class Book
{
  public $id;
  public $score;

  public function __construct($id, $score)
  {
      $this->id = $id;
      $this->score = $score;
  }
}

class Library
{
  public $id;
  public $booksInLibrary;
  public $signupTime;
  public $maxBooksShippedDaily;

  public function __construct($id, $booksInLibrary, $signupTime, $maxBooksShippedDaily)
  {
      $this->id = $id;
      $this->booksInLibrary = $booksInLibrary;
      $this->signupTime = $signupTime;
      $this->maxBooksShippedDaily = $maxBooksShippedDaily;
  }
}

/* helper functions */

/* read input */

$SCORE = 0; // increment this during the solution to track the points
$books = $libraries = [];

$fileManager = new FileManager($fileName);
$fileContent = $fileManager->get();

$fileRows = explode("\n", $fileContent);
list($numBooks, $numLibraries, $numDays) = explode(' ', $fileRows[0]);
$rawBooks = explode(" ", $fileRows[1]);

foreach($rawBooks as $index => $score) {
  $books[] = new Book($index, $score);
}

$startLine = 2;
for ($i = 0; $i < $numLibraries; $i++) {
  list($numBooksInLib, $daysForSignup, $maxBooksPerDay) = explode(' ', $fileRows[$i + $startLine]);
  $booksInLib = explode(" ", $fileRows[$i + $startLine + 1]);
  $libraries[] = new Library($i, $booksInLib, $daysForSignup, $maxBooksPerDay);
  $startLine++;
}

Log::out("Finito di leggere input", 0, "white", "red");
