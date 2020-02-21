<?php

use Utils\FileManager;
use Utils\Log;

require_once '../../bootstrap.php';

/* classes */

class Book
{
    public $id;
    public $score;
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
        $booksTmp = $this->booksInLibrary;
        uasort($booksTmp, ['Library', 'cmp_books']);

        for ($i = 0; $i < $daysRemaining && count($booksTmp) > 0; $i++) {
            for ($j = 0; $j < $this->maxBooksShippedDaily; $j++) {
                $book = array_shift(array_slice($booksTmp, 0, 1));
                if ($book != null) {
                    removeBookFromLibraries($book);
                    array_shift($booksTmp);
                    $this->booksShipped[] = $book->id;
                    $SCORE += $book->score;
                }
            }
        }
        $this->alreadyDone = true;
    }

    public function totScore()
    {
        $tot = 0;
        foreach ($this->booksInLibrary as $index => $book) {
            $tot += $book->score;
        }
        return $tot;
    }

    public function totOccurrencies()
    {
        $occurrencies = 0;
        foreach ($this->booksInLibrary as $index => $book) {
            $occurrencies += $book->occurrencies;
        }
        return $occurrencies;
    }

    public function avgBookScore()
    {
        $tot = 0;
        foreach ($this->booksInLibrary as $index => $book) {
            $tot += $book->score;
        }
        return $tot / count($this->booksInLibrary);
    }

    public function runningTime()
    {
        return count($this->booksInLibrary) / $this->maxBooksShippedDaily;
    }

    public function cmp_books($a, $b)
    {
        if ($a->score == $b->score) {
            return 0;
        }
        return ($a->score > $b->score) ? -1 : 1;
    }
}

/* read input */

$SCORE = 0; // increment this during the solution to track the points
$books = $libraries = [];

$fileManager = new FileManager($fileName);
$fileContent = $fileManager->get();

$fileRows = explode("\n", $fileContent);
list($numBooks, $numLibraries, $numDays) = explode(' ', $fileRows[0]);
$rawBooks = explode(" ", $fileRows[1]);

foreach ($rawBooks as $index => $score) {
    $books[$index] = new Book($index, intval($score));
}

$startLine = 2;
for ($i = 0; $i < $numLibraries; $i++) {
    list($numBooksInLib, $daysForSignup, $maxBooksPerDay) = explode(' ', $fileRows[$i + $startLine]);
    $idBooks = explode(" ", $fileRows[$i + $startLine + 1]);
    $booksInLib = [];
    foreach ($idBooks as $index => $bookId) {
        $books[intval($bookId)]->occurrencies++;
        $booksInLib[intval($bookId)] = $books[intval($bookId)];
    }
    $libraries[] = new Library($i, $booksInLib, $daysForSignup, $maxBooksPerDay);
    $startLine++;
}

Log::out("Finito di leggere input", 0, "white", "red");
