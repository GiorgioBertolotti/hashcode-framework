<?php

use Utils\FileManager;
use Utils\Log;

require_once '../../bootstrap.php';

/* classes */

class Book
{
  public $id;
  public $score;
  public $occurrencies = 0;
  public $localScore;

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
  public $localScore;
  public $alreadyDone = false;
  public $booksShipped = [];

  public function __construct($id, $booksInLibrary, $signupTime, $maxBooksShippedDaily)
  {
    $this->id = $id;
    $this->booksInLibrary = $booksInLibrary;
    $this->signupTime = $signupTime;
    $this->maxBooksShippedDaily = $maxBooksShippedDaily;
  }

  public function start()
  {
    global $daysRemaining, $SCORE;
    $daysRemaining -= $this->signupTime;
    $booksRemaining = $this->booksInLibrary;
    for ($i = 0; $i < $daysRemaining && count($booksRemaining) > 0; $i++) {
      for ($j = 0; $j < $this->maxBooksShippedDaily; $j++) {
        $book = $this->getBestBook();
        if ($book != null) {
          removeBookFromLibraries($book);
          $this->booksShipped[] = $book->id;
          $SCORE += $book->score;
        }
      }
    }
    $this->alreadyDone = true;
  }

  public function getBestBook()
  {
    /*
    global $books;
    $bestBook = 0;
    $maxScore = $books[$this->booksInLibrary[0]]->score;
    for ($i = 1; $i < count($this->booksInLibrary); $i++) {
      $book = $books[$this->booksInLibrary[$i]];
      if ($book->score > $maxScore) {
        $bestBook = $book;
        $maxScore = $book->score;
      }
    }
    return $bestBook;
    */
    if (count($this->booksInLibrary) == 0)
      return null;
    return array_shift(array_slice($this->booksInLibrary, 0, 1));
  }
}

/* helper functions */

function array_sort_by_column(&$arr, $col, $dir = SORT_DESC)
{
  $sort_col = array();
  foreach ($arr as $key => $row) {
    $sort_col[$key] = $row[$col];
  }

  array_multisort($sort_col, $dir, $arr);
}


array_sort_by_column($array, 'order');

/* read input */

$SCORE = 0; // increment this during the solution to track the points
$books = $libraries = [];

$fileManager = new FileManager($fileName);
$fileContent = $fileManager->get();

$fileRows = explode("\n", $fileContent);
list($numBooks, $numLibraries, $numDays) = explode(' ', $fileRows[0]);
$rawBooks = explode(" ", $fileRows[1]);

foreach ($rawBooks as $index => $score) {
  $books[$index] = new Book($index, $score);
}

$startLine = 2;
for ($i = 0; $i < $numLibraries; $i++) {
  list($numBooksInLib, $daysForSignup, $maxBooksPerDay) = explode(' ', $fileRows[$i + $startLine]);
  $idBooks = explode(" ", $fileRows[$i + $startLine + 1]);
  $booksInLib = [];
  foreach ($idBooks as $index => $bookId) {
    $books[$bookId]->occurrencies++;
    $booksInLib[$bookId] = $books[$bookId];
  }
  $libraries[] = new Library($i, $booksInLib, $daysForSignup, $maxBooksPerDay);
  $startLine++;
}

Log::out("Finito di leggere input", 0, "white", "red");
