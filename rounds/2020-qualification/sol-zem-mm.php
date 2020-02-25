<?php

$fileName = 'd';

include 'reader-mm.php';

/**
 * @var int $countBooks
 * @var int $countLibraries
 * @var int $countDays
 * @var Book[] $books
 * @var Library[] $libraries
 */

/** @var int $totalScore */
$totalScore = 0;
/** @var Book[] $scannedBooks */
$scannedBooks = [];
/** @var Book[] $notScannedBooks */
$notScannedBooks = $books;
/** @var Library[] $orderedSignuppedLibraries */
$orderedSignuppedLibraries = [];
/** @var Library[] $signuppedLibraries */
$signuppedLibraries = [];
/** @var Library[] $notSignuppedLibraries */
$notSignuppedLibraries = $libraries;
/** @var Library $currentSignupLibrary */
$currentSignupLibrary = null;

// Algo

foreach ($books as $b) {
  $b->rAward = $b->award / pow(count($b->inLibraries), 1);
}
foreach ($libraries as $l) {
  foreach ($l->books as $b) {
    $l->rCurrentTotalAward += $b->rAward;
  }
}

for ($t = 0; $t < $countDays; $t++) {

  echo "[t = {$t}]\n";

  // Controllo se ha finito il signup la library corrente
  if ($currentSignupLibrary !== null && $t >= $currentSignupLibrary->signupFinishAt) {
    $currentSignupLibrary->finishSignup();
    $signuppedLibraries[$currentSignupLibrary->id] = $currentSignupLibrary;
    $orderedSignuppedLibraries[$currentSignupLibrary->id] = $currentSignupLibrary;
    $currentSignupLibrary = null;
    uasort($signuppedLibraries, function (Library $l1, Library $l2) {
      return count($l1->books) < count($l2->books);
    });
  }

  // Per le library già signuppate scanno i libri in ordine di award e al max shipsPerDay
  foreach ($signuppedLibraries as $sl) {
    $count = 0;
    foreach ($sl->books as $b) {
      /*
            if (isset($scannedBooks[$b->id])) {
                die("Errore");
            }
            */
      $scannedBooks[$b->id] = $b;
      $totalScore += $b->award;
      // Tolgo i libri dalle altre library che lo hanno
      $b->scan($sl);
      $count++;
      if ($count >= $sl->shipsPerDay) break;
    }
  }

  if ($currentSignupLibrary === null) {
    // Do degli score alle library non ancora signuppate
    $libraryScores = [];
    $remainingTime = $countDays - $t;
    foreach ($notSignuppedLibraries as $nsl) {
      $avanzo = $remainingTime - $nsl->signUpDuration;
      if ($avanzo > 0) {
        //$finishesInTime = ($nsl->runningTime() + $nsl->signUpDuration) < $remainingTime;
        //$score = $nsl->rCurrentTotalAward * ($finishesInTime ? 10 : 1) / $nsl->signUpDuration;
        $score = count($nsl->books);
        //$score = $nsl->rCurrentTotalAward / $nsl->signUpDuration * $avanzo / $remainingTime;
        //$score = $nsl->currentTotalAward * $nsl->shipsPerDay / $nsl->signUpDuration * $avanzo;
        //$score = $nsl->currentTotalAward * $nsl->shipsPerDay;
        //$score = pow($nsl->currentTotalAward * $nsl->shipsPerDay, 0.71) / $nsl->signUpDuration * pow($avanzo / $remainingTime, 9);

        $libraryScores[$nsl->id] = $score;
      }
    }
    arsort($libraryScores);

    // Prendo la migliore e Faccio partire il processo di signup per la library con lo score max
    reset($libraryScores);
    $bestLibraryId = key($libraryScores);
    if ($bestLibraryId !== null /*&& $t + $notSignuppedLibraries[$bestLibraryId]->signUpDuration < $countDays*/) {
      $currentSignupLibrary = $notSignuppedLibraries[$bestLibraryId];
      $currentSignupLibrary->startSignup($t);
      unset($notSignuppedLibraries[$currentSignupLibrary->id]);
    }
  }
}

echo "\n\nTotal score: {$totalScore}";

// Output

$output = '';
foreach ($orderedSignuppedLibraries as $lId => $sl) {
  if (count($sl->scannedBooks) === 0) {
    unset($orderedSignuppedLibraries[$lId]);
  }
}
$output .= count($signuppedLibraries) . "\n";
foreach ($orderedSignuppedLibraries as $sl) {
  $scannedBooksCount = count($sl->scannedBooks);
  $output .= "{$sl->id} {$scannedBooksCount}\n";
  $scannedIds = [];
  foreach ($sl->scannedBooks as $sb) {
    $scannedIds[] = $sb->id;
  }
  $output .= implode(" ", $scannedIds) . "\n";
}

$fileManager->output($output);
