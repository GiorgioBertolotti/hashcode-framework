<?php

use Utils\Log;
use Utils\Stopwatch;

require_once '../../bootstrap.php';

$fileName = 'a';

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

class Company
{
  public $name;
  public $developers = [];
  public $managers = [];

  public function __construct($name)
  {
    $this->name = $name;
  }

  public function placeDev($r, $c)
  {
    for ($i = 0; $i < count($this->developers); $i++) {
      $developer = $this->developers[$i];
      if (!$developer->placed) {
        $developer->placed = true;
        $developer->r = $r;
        $developer->c = $c;
        break;
      }
    }
  }

  public function placeManager($r, $c)
  {
    for ($i = 0; $i < count($this->managers); $i++) {
      $manager = $this->managers[$i];
      if (!$manager->placed) {
        $manager->placed = true;
        $manager->r = $r;
        $manager->c = $c;
        break;
      }
    }
  }
}

function getReplayerType($char)
{
  if ($char == "_")
    return "Developer";
  elseif ($char == 'M')
    return "ProjectManager";
  else
    return null;
}

function getConnectedComponents()
{
  global $office, $labels;
  $labelCount = 0;
  for ($r = 0; $r < count($office); $r++) {
    $row = $office[$r];
    for ($c = 0; $c < count($row); $c++) {
      $cell = $row[$c];
      if ($cell == '#') {
        $labels[$r][$c] = null;
        continue;
      }
      if ($labels[$r][$c] === null) {
        labelCC($labelCount++, $r, $c);
      }
    }
  }
  return $labelCount;
}

function labelCC($label, $r, $c)
{
  global $office, $labels, $ccs;
  $labels[$r][$c] = $label;
  $ccs[$label][] = [$r, $c];
  $queue = new SplQueue();
  do {
    if ($c > 0) {
      $leftSit = $office[$r][$c - 1];
      $leftLabel = $labels[$r][$c - 1];
      if ($leftSit != '#' && $leftLabel === null) {
        // c'è un posto libero a sinistra
        $labels[$r][$c - 1] = $label;
        $ccs[$label][] = [$r, $c - 1];
        $queue->enqueue([$r, $c - 1]);
      }
    }
    if ($c != count($office[0]) - 1) {
      $rightSit = $office[$r][$c + 1];
      $rightLabel = $labels[$r][$c + 1];
      if ($rightSit != '#' && $rightLabel === null) {
        // c'è un posto libero a destra
        $labels[$r][$c + 1] = $label;
        $ccs[$label][] = [$r, $c + 1];
        $queue->enqueue([$r, $c + 1]);
      }
    }
    if ($r > 0) {
      $upSit = $office[$r - 1][$c];
      $upLabel = $labels[$r - 1][$c];
      if ($upSit != '#' && $upLabel === null) {
        // c'è un posto libero sopra
        $labels[$r - 1][$c] = $label;
        $ccs[$label][] = [$r - 1, $c];
        $queue->enqueue([$r - 1, $c]);
      }
    }
    if ($r != count($office) - 1) {
      $downSit = $office[$r + 1][$c];
      $downLabel = $labels[$r + 1][$c];
      if ($downSit != '#' && $downLabel === null) {
        // c'è un posto libero sotto
        $labels[$r + 1][$c] = $label;
        $ccs[$label][] = [$r + 1, $c];
        $queue->enqueue([$r + 1, $c]);
      }
    }
    if (!$queue->isEmpty()) {
      $pos = $queue->dequeue();
      $r = $pos[0];
      $c = $pos[1];
    }
  } while (!$queue->isEmpty());
}

Stopwatch::tik('Mappa');

$peopleInOffice = [];

for ($r = 0; $r < count($office); $r++) {
  $row = $office[$r];
  for ($c = 0; $c < count($row); $c++) {
    $cell = $row[$c];
    $peopleInOffice[$r][$c] = null;
  }
}

$companies = [];
for ($i = 0; $i < count($developers); $i++) {
  $developer = $developers[$i];
  if ($companies[$developer->company] == null) {
    $companies[$developer->company] = new Company($developer->company);
    $companies[$developer->company]->developers[] = $developer;
  } else
    $companies[$developer->company]->developers[] = $developer;
}
for ($i = 0; $i < count($managers); $i++) {
  $manager = $managers[$i];
  if ($companies[$manager->company] == null) {
    $companies[$manager->company] = new Company($manager->company);
    $companies[$manager->company]->managers[] = $manager;
  } else
    $companies[$manager->company]->managers[] = $manager;
}

$labels = [];
$ccs = [];
$numCC = getConnectedComponents();

for ($label = 0; $label < $numCC; $label++) {
  $numDevelopers = 0;
  $numManagers = 0;
  for ($i = 0; $i < count($ccs[$label]); $i++) {
    $cellPos = $ccs[$label][$i];
    $cell = $labels[$cellPos[0]][$cellPos[1]];
    if ($cell === $label) {
      if ($office[$cellPos[0]][$cellPos[1]] == '_')
        $numDevelopers++;
      else
        $numManagers++;
    }
  }
  echo $numDevelopers . " " . $numManagers . PHP_EOL;

  $filled = false;
  foreach ($companies as $companyId => $company) {
    $compDevs = count($company->developers);
    $compManagers = count($company->managers);
    if ($numDevelopers == $compDevs && $numManagers == $compManagers) {
      $filled = true;
      for ($i = 0; $i < count($ccs[$label]); $i++) {
        $cellPos = $ccs[$label][$i];
        $cell = $labels[$cellPos[0]][$cellPos[1]];
        if ($cell === $label) {
          if ($office[$cellPos[0]][$cellPos[1]] == '_')
            $company->placeDev($cellPos[0], [$cellPos[1]]);
          else
            $company->placeManager($cellPos[0], [$cellPos[1]]);
        }
      }
    }
  }
  if (!$filled) {
    foreach ($companies as $companyId => $company) {
      $compDevs = count($company->developers);
      $compManagers = count($company->managers);
      $numDevelopers -= $compDevs;
      $numManagers -= $compManagers;
      for ($i = 0; $i < min($compDevs + $compManagers, count($ccs[$label])); $i++) {
        $cellPos = $ccs[$label][$i];
        $cell = $labels[$cellPos[0]][$cellPos[1]];
        if ($cell === $label) {
          if ($office[$cellPos[0]][$cellPos[1]] == '_')
            $company->placeDev($cellPos[0], $cellPos[1]);
          else
            $company->placeManager($cellPos[0], $cellPos[1]);
        }
      }
      $ccs[$label] = array_splice($ccs[$label], $i, count($ccs[$label]));
      if ($numDevelopers == 0 && $numManagers == 0)
        break;
    }
  }
}

// for ($r = 0; $r < count($office); $r++) {
//   $row = $office[$r];
//   for ($c = 0; $c < count($row); $c++) {
//     $cell = $row[$c];
//     if ($cell == '#')
//       continue;
//   }
// }

$output = [];
for ($i = 0; $i < count($developers); $i++) {
  $developer = $developers[$i];
  if ($developer->placed) {
    $output[] = $developer->c . " " . $developer->r;
  } else {
    $output[] = "X";
  }
}
for ($i = 0; $i < count($managers); $i++) {
  $manager = $managers[$i];
  if ($manager->placed) {
    $output[] = $manager->c . " " . $manager->r;
  } else {
    $output[] = "X";
  }
}
$fileManager->output(implode("\n", $output));

Stopwatch::tok('Mappa');
Stopwatch::print('Mappa');
