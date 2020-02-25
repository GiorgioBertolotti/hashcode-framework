<?php

use Utils\Log;
use Utils\Stopwatch;

$fileName = 'c';

require_once 'reader.php';

/* functions */

function calculateLibraryScore(Library $library)
{
    global $daysRemaining;
    $boost = ($library->runningTime() + $library->signupTime) < $daysRemaining;
    return ($library->totScore()) * ($boost ? 10 : 1) / $library->signupTime;
    //return ($library->maxBooksShippedDaily * count($library->booksInLibrary)) / $library->signupTime;
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
        if ($library->localScore == 0) {
            $libraries[$i] = $libraries[0];
            array_shift($libraries);
            continue;
        }
        if ($library->signupTime > $daysRemaining) {
            continue;
        }
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
    Log::out("Starting library: " . $library->id, 0);
    $library->start();
    Log::out("Remaining libraries: " . (count($libraries) - $i), 0);
}

Log::out("SCORE: " . $SCORE, 0);

$output = [];
$countLibs = 0;
for ($i = 0; $i < count($libraries); $i++) {
    $library = $libraries[$i];
    $countBooksShipped = count($library->booksShipped);
    if ($countBooksShipped > 0) {
        $output[] = $library->id . " " . count($library->booksShipped);
        $output[] = implode(" ", $library->booksShipped);
        $countLibs++;
    }
}
array_unshift($output, $countLibs);
$fileManager->output(implode("\n", $output));

//print_r($libraries);

Stopwatch::tok('Totale');
Stopwatch::print('Totale');