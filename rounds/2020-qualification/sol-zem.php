<?php

use Utils\Stopwatch;

$fileName = 'a';

require_once 'reader.php';

/* functions */

function calculateLibraryScore(Library $library)
{
  return ($library->maxBooksShippedDaily * count($library->booksInLibrary)) / $library->signupTime;
}

function removeBookFromLibraries(Book $book)
{
  global $libraries;
  foreach ($libraries as $index => $library) {
    unset($library->booksInLibrary[$book->id]);
  }
}

function getBestLibrary()
{
  global $libraries, $daysRemaining;
  $bestLibrary = null;
  for ($i = 0; $i < count($libraries); $i++) {
    $library = $libraries[$i];
    if ($library->alreadyDone)
      continue;
    $library->localScore = calculateLibraryScore($library);
    if ($library->signupTime > $daysRemaining)
      continue;
    if ($bestLibrary == null || $library->localScore > $bestLibrary->localScore) {
      $bestLibrary = $library;
    }
  }
  return $bestLibrary;
}

/* runtime */
Stopwatch::tik('Totale');

$daysRemaining = $numDays;

for ($i = 0; $i < count($libraries); $i++) {
  $library = getBestLibrary();
  if ($library == null)
    break;
  $library->start();
}

print_r($libraries);

Stopwatch::tok('Totale');
Stopwatch::print('Totale');
