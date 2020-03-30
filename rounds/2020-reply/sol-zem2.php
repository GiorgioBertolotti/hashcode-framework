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

function searchCompany($c1, $c2)
{
  global $companies;
  $type1 = getReplayerType($c1);
  $type2 = getReplayerType($c2);
  $biggestCompany = null;
  foreach ($companies as $companyId => $company) {
    $type1Found = false;
    $type2Found = false;
    if ($type1 == "Developer") {
      foreach ($company->developers as $developer) {
        if (!$developer->placed) {
          $type1Found = true;
          break;
        }
      }
    } else {
      foreach ($company->managers as $manager) {
        if (!$manager->placed) {
          $type1Found = true;
          break;
        }
      }
    }
    if ($type2 == "Developer") {
      $count = 0;
      foreach ($company->developers as $developer) {
        if (!$developer->placed) {
          if ($type1 == $type2) {
            if ($count > 0) {
              $type2Found = true;
              break;
            } else
              $count++;
          } else {
            $type2Found = true;
            break;
          }
        }
      }
    } else {
      $count = 0;
      foreach ($company->managers as $manager) {
        if (!$manager->placed) {
          if ($type1 == $type2) {
            if ($count > 0) {
              $type2Found = true;
              break;
            } else
              $count++;
          } else {
            $type2Found = true;
            break;
          }
        }
      }
    }
    if ($type1Found && $type2Found) {
      if ($biggestCompany == null) {
        $biggestCompany = $companyId;
      } elseif (count($company) > count($companies[$companyId])) {
        $biggestCompany = $companyId;
      }
    }
  }
  return $biggestCompany;
}

// function getConnectedComponents($r, $c)
// {
//   global $office;
//   $labels = [];
//   $ccs = [];
//   $labelCount = 0;
//   for ($r = 0; $r < count($office); $r++) {
//     $row = $office[$r];
//     for ($c = 0; $c < count($row); $c++) {
//       $cell = $row[$c];
//       if ($cell == '#') {
//         $labels[$r][$c] = null;
//         continue;
//       }
//       if ($labels[$r][$c] == null) {
//         labelCC($labels, $ccs, $labelCount++, $r, $c);
//       }
//     }
//   }
// }

// function labelCC($labels, $ccs, $label, $r, $c) {
//   global $office;
//   if ($c != count($row) - 1) {
//     $rightSit = $office[$r][$c + 1];
//     $rightSitPerson = $peopleInOffice[$r][$c + 1];
//     if ($rightSit != '#' && $rightSitPerson == null) {
//       // c'è un posto libero a destra
//       Log::out($cell . " ha un posto libero a destra: " . $rightSit, 0);
//       $company = searchCompany($cell, $rightSit);
//       if ($company != null) {
//         Log::out("Ci starebbe " . $company, 0);
//       }
//     }
//   }
//   if ($r != count($office) - 1) {
//     $downSit = $office[$r + 1][$c];
//     $downSitPerson = $peopleInOffice[$r + 1][$c];
//     if ($downSit != '#' && $downSitPerson == null) {
//       // c'è un posto libero sotto
//       Log::out($cell . " ha un posto libero sotto: " . $downSit, 0);
//       $company = searchCompany($cell, $downSit);
//       if ($company != null) {
//         Log::out("Ci starebbe " . $company, 0);
//       }
//     }
//   }
// }

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

for ($r = 0; $r < count($office); $r++) {
  $row = $office[$r];
  for ($c = 0; $c < count($row); $c++) {
    $cell = $row[$c];
    if ($cell == '#')
      continue;
    if ($peopleInOffice[$r][$c] != null) {
      $replayer = $peopleInOffice[$r][$c];
      $company = $replayer->company;
      if ($c != count($row) - 1) {
        $rightSit = $office[$r][$c + 1];
        $rightSitPerson = $peopleInOffice[$r][$c + 1];
        if ($rightSit != '#' && $rightSitPerson == null) {
          // c'è un posto libero a destra
          Log::out($cell . " è già piazzato ed ha un posto libero a destra: " . $rightSit, 0);
          print_r(getReplayerType($cell));
          if (getReplayerType($cell) == "Developer") {
            foreach ($company->developers as $developer) {
              if (!$developer->placed) {
                $developer->placed = true;
                $developer->r = $r;
                $developer->c = $c;
                $peopleInOffice[$r][$c] = $developer;
                break;
              }
            }
          } else {
            foreach ($company->managers as $manager) {
              if (!$manager->placed) {
                $manager->placed = true;
                $manager->r = $r;
                $manager->c = $c;
                $peopleInOffice[$r][$c] = $manager;
                break;
              }
            }
          }
          if (getReplayerType($rightSit) == "Developer") {
            foreach ($company->developers as $developer) {
              if (!$developer->placed) {
                $developer->placed = true;
                $developer->r = $r;
                $developer->c = $c;
                $peopleInOffice[$r][$c + 1] = $developer;
                break;
              }
            }
          } else {
            foreach ($company->managers as $manager) {
              if (!$manager->placed) {
                $manager->placed = true;
                $manager->r = $r;
                $manager->c = $c;
                $peopleInOffice[$r][$c + 1] = $manager;
                break;
              }
            }
          }
        }
      }
      if ($r != count($office) - 1) {
        $downSit = $office[$r + 1][$c];
        $downSitPerson = $peopleInOffice[$r + 1][$c];
        if ($downSit != '#' && $downSitPerson == null) {
          // c'è un posto libero sotto
          Log::out($cell . " è già piazzato ed ha un posto libero sotto: " . $downSit, 0);
          if (getReplayerType($cell) == "Developer") {
            foreach ($company->developers as $developer) {
              if (!$developer->placed) {
                $developer->placed = true;
                $developer->r = $r;
                $developer->c = $c;
                $peopleInOffice[$r][$c] = $developer;
                break;
              }
            }
          } else {
            foreach ($company->managers as $manager) {
              if (!$manager->placed) {
                $manager->placed = true;
                $manager->r = $r;
                $manager->c = $c;
                $peopleInOffice[$r][$c] = $manager;
                break;
              }
            }
          }
          if (getReplayerType($rightSit) == "Developer") {
            foreach ($company->developers as $developer) {
              if (!$developer->placed) {
                $developer->placed = true;
                $developer->r = $r;
                $developer->c = $c;
                $peopleInOffice[$r + 1][$c] = $developer;
                break;
              }
            }
          } else {
            foreach ($company->managers as $manager) {
              if (!$manager->placed) {
                $manager->placed = true;
                $manager->r = $r;
                $manager->c = $c;
                $peopleInOffice[$r + 1][$c] = $manager;
                break;
              }
            }
          }
        }
      }
    } else {
      if ($c != count($row) - 1) {
        $rightSit = $office[$r][$c + 1];
        $rightSitPerson = $peopleInOffice[$r][$c + 1];
        if ($rightSit != '#' && $rightSitPerson == null) {
          // c'è un posto libero a destra
          Log::out($cell . " ha un posto libero a destra: " . $rightSit, 0);
          $companyId = searchCompany($cell, $rightSit);
          if ($companyId != null) {
            Log::out("Ci starebbe " . $companyId, 0);
            $company = $companies[$companyId];
            if (getReplayerType($cell) == "Developer") {
              foreach ($company->developers as $developer) {
                if (!$developer->placed) {
                  $developer->placed = true;
                  $developer->r = $r;
                  $developer->c = $c;
                  $peopleInOffice[$r][$c] = $developer;
                  break;
                }
              }
            } else {
              foreach ($company->managers as $manager) {
                if (!$manager->placed) {
                  $manager->placed = true;
                  $manager->r = $r;
                  $manager->c = $c;
                  $peopleInOffice[$r][$c] = $manager;
                  break;
                }
              }
            }
            if (getReplayerType($rightSit) == "Developer") {
              foreach ($company->developers as $developer) {
                if (!$developer->placed) {
                  $developer->placed = true;
                  $developer->r = $r;
                  $developer->c = $c + 1;
                  $peopleInOffice[$r][$c + 1] = $developer;
                  break;
                }
              }
            } else {
              foreach ($company->managers as $manager) {
                if (!$manager->placed) {
                  $manager->placed = true;
                  $manager->r = $r;
                  $manager->c = $c + 1;
                  $peopleInOffice[$r][$c + 1] = $manager;
                  break;
                }
              }
            }
          }
        }
      }
      if ($r != count($office) - 1) {
        $downSit = $office[$r + 1][$c];
        $downSitPerson = $peopleInOffice[$r + 1][$c];
        if ($downSit != '#' && $downSitPerson == null) {
          // c'è un posto libero sotto
          Log::out($cell . " ha un posto libero sotto: " . $downSit, 0);
          $companyId = searchCompany($cell, $downSit);
          if ($companyId != null) {
            Log::out("Ci starebbe " . $companyId, 0);
            $company = $companies[$companyId];
            if (getReplayerType($cell) == "Developer") {
              foreach ($company->developers as $developer) {
                if (!$developer->placed) {
                  $developer->placed = true;
                  $developer->r = $r;
                  $developer->c = $c;
                  $peopleInOffice[$r][$c] = $developer;
                  break;
                }
              }
            } else {
              foreach ($company->managers as $manager) {
                if (!$manager->placed) {
                  $manager->placed = true;
                  $manager->r = $r;
                  $manager->c = $c;
                  $peopleInOffice[$r][$c] = $manager;
                  break;
                }
              }
            }
            if (getReplayerType($rightSit) == "Developer") {
              foreach ($company->developers as $developer) {
                if (!$developer->placed) {
                  $developer->placed = true;
                  $developer->r = $r + 1;
                  $developer->c = $c;
                  $peopleInOffice[$r + 1][$c] = $developer;
                  break;
                }
              }
            } else {
              foreach ($company->managers as $manager) {
                if (!$manager->placed) {
                  $manager->placed = true;
                  $manager->r = $r + 1;
                  $manager->c = $c;
                  $peopleInOffice[$r + 1][$c] = $manager;
                  break;
                }
              }
            }
          }
        }
      }
    }
  }
}

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
