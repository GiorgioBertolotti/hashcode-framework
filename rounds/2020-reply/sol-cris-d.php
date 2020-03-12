<?php

use Utils\Log;
use Utils\Stopwatch;
use Utils\Cache;

require_once '../../bootstrap.php';

$fileName = 'd';

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

function placed($dev) {
    global $developers;
    return $developers[$dev->id]->placed;
}

function cmpFriends($dev1, $dev2) {
    return $dev1->score < $dev2->score;
}

Stopwatch::tik('Tuple');

$tuple = [];
$bestFriends = [];

$countDev = 1;
$totalDev = count($developers);

foreach ($developers AS $dev1) {
    $bestFriends[$dev1->id] = [];
    foreach ($developers AS $dev2) {
        if ($dev1==$dev2)
            continue;
        $score = calculateBonus($dev1, $dev2);
        if ($score > 0) {
            $tupla = new Tupla($dev1, $dev2, $score);
            $bestFriends[$dev1->id][] = $tupla;
            $tuple[] = $tupla;
        }
    }
    Log::out("Stato (step 1): " . $countDev++ . " / " . $totalDev, 0);
}

// Ordiniamo tutto per bene
usort($tuple, cmpFriends);
foreach ($developers AS $dev1) {
    usort($bestFriends[$dev1->id], cmpFriends);
    Log::out("Stato (step 2): " . $countDev++ . " / " . $totalDev, 0);
}

$totalScore = 0;
$disposizioneDeveloper = [];
$posizionati = 1;

$disposizioneDeveloper[] = $tuple[0]->repl1;
$developers[$tuple[0]->repl1->id]->placed = true;
unset($tuple[0]);
while ($posizionati<count($developers)) {
    Log:out("Stato (step 3): " . $posizionati . " / " . $totalDev, 0);
    do {
        $successivo = $bestFriends[$disposizioneDeveloper[$posizionati-1]->id][0];
        unset($bestFriends[$disposizioneDeveloper[$posizionati-1]][0]);
    } while ((placed($successivo->repl1) && placed($successivo->repl2)) && count($bestFriends[$disposizioneDeveloper[$posizionati-1]])>0);

    if (placed($successivo->repl1) && placed($successivo->repl2)) {
        foreach ($tuple AS $tupla) {
            $successivo = $tupla;
            if ((!placed($successivo->repl1) && placed($successivo->repl2))) {
                break;
            }
        }
    }

    if (placed($successivo->repl1)) {
        $disposizioneDeveloper[] = $successivo->repl2;
        $developers[$successivo->repl2->id]->placed = true;
        $posizionati++;
    }
    elseif (placed($successivo->repl2)) {
        $disposizioneDeveloper[] = $successivo->repl1;
        $developers[$successivo->repl1->id]->placed = true;
        $posizionati++;
    }
    elseif (placed($successivo->repl1) && placed($successivo->repl2)) {
        break;
    }
}

print_r($disposizioneDeveloper);
file_put_contents("caches/disposizione_" .$fileName . ".txt", json_encode($disposizioneDeveloper));

Stopwatch::tok('Tuple');
Stopwatch::print('Tuple');
