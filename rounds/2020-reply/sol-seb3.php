<?php

use Utils\Log;

error_reporting(E_ALL);

require_once '../../bootstrap.php';

$fileName = 'b';

include 'reader-seb.php';

/** Stuff... */

/**cl
 * @param Employee $employeStart
 * @return mixed|null
 */
function getBestEmployeBySkills($employeStart)
{
    global $employees, $developers;

    if (is_object($employeStart)) {
        $bestScore = 0;
        $bestEmployeeByTop = null;
        foreach ($developers as $employee) {
            /** @var Employee $employee */
            if (empty($employees[$employee->id]->coordinates)) {
                if($employee->company == $employeStart->company)
                    $localScore = $employee->bonus * $employeStart->bonus;
                else $localScore = 0;

                $skillsComuni = count(array_intersect($employee->skills, $employeStart->skills));
                $skillsDiverse = max(count($employee->skills), count($employeStart->skills)) - $skillsComuni;
                /*if ($skillsComuni == count($employeStart->skills) && count($employee->skills) == count($employeStart->skills)) {
                    //le skills di entrambi i dipendenti sono identifici score = 0
                    continue;
                } else */if ($bestScore == 0 || $bestScore < ($skillsComuni * $skillsDiverse + $localScore)) {
                    $bestScore = ($skillsComuni * $skillsDiverse) + $localScore;
                    $bestEmployeeByTop = $employee;
                }
            } else continue;
        }

        return ['employee' => $bestEmployeeByTop, 'localScore' => $bestScore];
    } else
        return ['localScore' => 0];
}

function getBestDeveloperHere($rowId, $columnId)
{
    global $employees, $office, $developers, $mostPopularCompany, $worstPopularCompany;

    $topEmploye = $office[$rowId][$columnId - 1];
    $bestEmployeByTop = getBestEmployeBySkills($topEmploye);

    $rightEmploye = $office[$rowId + 1][$columnId];
    $bestEmployeByRight = getBestEmployeBySkills($rightEmploye);


    $bottomEmploye = $office[$rowId][$columnId + 1];
    $bestEmployeByBottom = getBestEmployeBySkills($bottomEmploye);

    $leftEmployee = $office[$rowId - 1][$columnId];
    $bestEmployeByLeft = getBestEmployeBySkills($leftEmployee);

    $bestScore = max([$bestEmployeByTop['localScore'], $bestEmployeByRight['localScore'], $bestEmployeByBottom['localScore'], $bestEmployeByLeft['localScore']]);

    if ($bestEmployeByTop['localScore'] && $bestEmployeByTop['localScore'] == $bestScore) return $bestEmployeByTop['employee'];
    else if ($bestEmployeByRight['localScore'] && $bestEmployeByRight['localScore'] == $bestScore) return $bestEmployeByRight['employee'];
    else if ($bestEmployeByBottom['localScore'] && $bestEmployeByBottom['localScore'] == $bestScore) return $bestEmployeByBottom['employee'];
    else if ($bestEmployeByLeft['localScore'] && $bestEmployeByLeft['localScore'] == $bestScore) return $bestEmployeByLeft['employee'];
    else {
        $returnDeveloper = null;
        //usort($developers2, "cmp2");
        foreach ($developers as $developer) {
            if (empty($employees[$developer->id]->coordinates) && $developer->company == $mostPopularCompany) {
                $returnDeveloper = $developer;
                break;
            }
        }

        if (is_null($returnDeveloper)) {
            foreach ($developers as $developer) {
                if (empty($developer->coordinates) && $developer->company == $worstPopularCompany) {
                    $returnDeveloper = $developer;
                    break;
                }
            }
        }

        if (is_null($returnDeveloper)) {
            foreach ($developers as $developer) {
                if (empty($developer->coordinates) && $developer->company == $worstPopularCompany) {
                    $returnDeveloper = $developer;
                    break;
                }
            }
        }

        return $returnDeveloper;
    }
}

function getBestManagerHere($rowId, $columnId)
{
    global $managers, $office, $mostPopularCompany, $employees;

    $topEmploye = $office[$rowId - 1][$columnId];
    $rightEmploye = $office[$rowId][$columnId + 1];
    $bottomEmploye = $office[$rowId + 1][$columnId];
    $leftEmployee = $office[$rowId][$columnId - 1];

    $arrayCompanies = [
        is_object($topEmploye) ? $topEmploye->company : null,
        is_object($rightEmploye) ? $rightEmploye->company : null,
        is_object($bottomEmploye) ? $bottomEmploye->company : null,
        is_object($leftEmployee) ? $leftEmployee->company : null
    ];

    $companies = array_count_values($arrayCompanies);
    arsort($companies);
    $mostPopularCompanyHere = array_slice(array_keys($companies), 0, 1, true)[0];
    if (!$mostPopularCompanyHere) {
        $mostPopularCompanyHere = $mostPopularCompany;
    }

    $bestManager = null;
    foreach ($managers as $manager) {
        if (!empty($manager->coordinates)) continue;

        if ($mostPopularCompanyHere) {
            if ($manager->company == $mostPopularCompanyHere) {
                $bestManager = $manager;
                break;
            }
        } else {
            // SUPER MIGLIORAMENTO prendere in questo caso il peggior manager con l'azienda piu scrausa e il bonus minore di tutti
            // ora viene preso il primo porca troia
            $bestManager = getWorstManager();
            break;
        }
    }

    return $bestManager;

}

function getWorstManager()
{
    global $managers, $worstPopularCompany, $employees;

    $managers2 = $managers;
    $keys = array_keys($managers2);
    array_multisort(
        array_column($managers2, 'bonus'), SORT_DESC, SORT_NUMERIC, $managers2, $keys
    );
    $managers2 = array_combine($keys, $managers2);

    foreach ($managers2 as $manager) {
        if (!empty($manager->coordinates)) continue;

        if ($manager->company == $worstPopularCompany)
            return $manager;
    }

    foreach($managers2 as $manager) {
        if (!empty($manager->coordinates)) continue;
        return $manager;
    }
}

$positions = [];

function order($a, $b)
{
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
}

function cmp($a, $b)
{
    if ($a->bonus == $b->bonus) {
        return 0;
    }
    return ($a->bonus > $b->bonus) ? -1 : 1;
}

function cmp3($a, $b)
{
    if ($a->bonus == $b->bonus) {
        return 0;
    }
    return ($a->bonus < $b->bonus) ? -1 : 1;
}

function cmp2($a, $b)
{
    if (count($a->skills) == count($b->skills)) {
        return 0;
    }
    return ((count($a->skills) * $a->bonus) > (count($b->skills) * $b->bonus)) ? -1 : 1;
}


krsort($companies);
$mostPopularCompany = array_keys($companies)[0];

asort($companies);
$worstPopularCompany = array_keys($companies)[0];

$output = [];

$posizionato = 0;

function ciclaPiuVeloce($rowId, &$columnId)
{
    global $office;

    if ($office[$rowId][$columnId] == '#' && $office[$rowId][$columnId + 1] == '#' && $office[$rowId][$columnId + 2] == '#' && $office[$rowId][$columnId + 3] == '#' && $office[$rowId][$columnId + 4] == '#') {
        $columnId = +5;
        return true;
    } elseif ($office[$rowId][$columnId] == '#' && $office[$rowId][$columnId + 1] == '#' && $office[$rowId][$columnId + 2] == '#' && $office[$rowId][$columnId + 3] == '#') {
        $columnId = +4;
        return true;
    } elseif ($office[$rowId][$columnId] == '#' && $office[$rowId][$columnId + 1] == '#' && $office[$rowId][$columnId + 2] == '#') {
        $columnId = +3;
        return true;
    }
    return false;
}

function posizionaQui($rowId, $columnId, $descrizione, &$office, &$developers, &$managers, &$employees)
{
    global $posizionato;
    // Posiziono quello a Destra
    if (in_array($office[$rowId][$columnId], ['_', 'M'])) {
        if ($office[$rowId][$columnId] == '_') {
            Log::out("Posizionando Developer a {$descrizione}  in [$rowId][" . $columnId . "]", 0);
            // Developer position
            $bestDeveloper = getBestDeveloperHere($rowId, $columnId);
            $posizionato++;
            $developers[$bestDeveloper->id]->coordinates = [$rowId, $columnId];
            $employees[$bestDeveloper->id]->coordinates = [$rowId, $columnId];
            $office[$rowId][$columnId] = $bestDeveloper;
        } else {
            $bestManager = getBestManagerHere($rowId, $columnId);
            if ($bestManager) {
                $posizionato++;
                Log::out("Posizionato Manager a {$descrizione} ID = $bestManager->id", 0);
                $employees[$bestManager->id]->coordinates = [$rowId, $columnId];
                $managers[$bestManager->id]->coordinates = [$rowId, $columnId];
                $office[$rowId][$columnId] = $bestManager;
            } else  Log::out("Nessun Manager trovato disponibile " . count($managers), 0);
        }
    }
}

$importanze = [];

$puntiMassimi = 10;

// punti cella developer = 2
// punti cella manager = 1
// punti cella # = 0

// Heating importanza inserimento delle celle
for ($rowId = 0; $rowId < $height; $rowId++) {
    for ($columnId = 0; $columnId < $width; $columnId++) {
        $puntiCella = $puntiMassimi;

        $cellaAttuale = $office[$rowId][$columnId];

        $cellaSopra = $office[$rowId - 1][$columnId];
        $cellaDestra = $office[$rowId][$columnId + 1];
        $cellaSotto = $office[$rowId + 1][$columnId];
        $cellaSinistra = $office[$rowId][$columnId - 1];

        if ($cellaSopra == '#' || is_null($cellaSopra)) {
            $puntiCella -= 2;
        } else if ($cellaSopra == 'M') {
            $puntiCella -= 1;
        }

        if ($cellaDestra == '#' || is_null($cellaDestra)) {
            $puntiCella -= 2;
        } else if ($cellaDestra == 'M') {
            $puntiCella -= 1;
        }

        if ($cellaSotto == '#' || is_null($cellaSotto)) {
            $puntiCella -= 2;
        } else if ($cellaSotto == 'M') {
            $puntiCella -= 1;
        }

        if ($cellaSinistra == '#' || is_null($cellaSinistra)) {
            $puntiCella -= 2;
        } else if ($cellaSinistra == 'M') {
            $puntiCella -= 1;
        }

        if ($cellaAttuale == '#' || is_null($cellaAttuale)) {
            $puntiCella = 0;
        } else if ($cellaAttuale == 'M') {
            $puntiCella -= 1;
        }

        $importanze[$rowId . '-' . $columnId] = $puntiCella;

    }
}

// Ordino le celle per importanza punti desc
array_multisort($importanze, SORT_DESC, array_keys($importanze));

// Ordino i $developers per bonus desc
$keys = array_keys($developers);
array_multisort(
    array_column($developers, 'bonus'), SORT_DESC, SORT_NUMERIC, $developers, $keys
);
$developers = array_combine($keys, $developers);

// Ordino i Managers per bonus desc
$keys = array_keys($managers);
array_multisort(
    array_column($managers, 'bonus'), SORT_DESC, SORT_NUMERIC, $managers, $keys
);
$managers = array_combine($keys, $managers);

// Partendo dalle celle con più importanza vado a posizionare
foreach ($importanze as $coordinates => $importanza) {
    if ($importanza == 0) continue;

    list($rowId, $columnId) = explode('-', $coordinates);

    $rowId = (int)$rowId;
    $columnId = (int)$columnId;

    if ($office[$rowId][$columnId] == '#' || is_object($office[$rowId][$columnId])) continue;

    if ($office[$rowId][$columnId] == '_') {
        Log::out("Posizionando Developer in [$rowId][$columnId]", 0);
        // Developer position
        $bestDeveloper = getBestDeveloperHere($rowId, $columnId);
        if ($bestDeveloper) {
            $posizionato++;
            //$developers[$bestDeveloper->id]->coordinates = [$rowId, $columnId];
            $employees[$bestDeveloper->id]->coordinates = [$rowId, $columnId];
            $office[$rowId][$columnId] = $bestDeveloper;
        } else Log::out("Nessun Developer trovato disponibile " . count($employees), 0);


        // Posiziono quello a Destra
        posizionaQui($rowId, $columnId + 1, 'destra', $office, $developers, $managers, $employees);

        // Posiziono quello in basso
        posizionaQui($rowId + 1, $columnId, 'basso', $office, $developers, $managers, $employees);
        // Posiziono quello in alto
        posizionaQui($rowId - 1, $columnId, 'alto', $office, $developers, $managers, $employees);

        // Posiziono quello a sinistra
        posizionaQui($rowId, $columnId - 1, 'sinistra', $office, $developers, $managers, $employees);


        // $ultimoPosizionato = $bestDeveloper;
    } else if ($office[$rowId][$columnId] == 'M') {
        //Manager position
        Log::out("Posizionando Manager in [$rowId][$columnId]", 0);
        $bestManager = getBestManagerHere($rowId, $columnId);
        if ($bestManager) {
            $posizionato++;
            Log::out("Posizionato Manager ID = $bestManager->id", 0);
            $employees[$bestManager->id]->coordinates = [$rowId, $columnId];
            //$managers[$bestManager->id]->coordinates = [$rowId, $columnId];
            $office[$rowId][$columnId] = $bestManager;
        } else  Log::out("Nessun Manager trovato disponibile " . count($managers), 0);


        // Posiziono quello a Destra
        posizionaQui($rowId, $columnId + 1, 'destra', $office, $developers, $managers, $employees);

        // Posiziono quello in basso
        posizionaQui($rowId + 1, $columnId, 'basso', $office, $developers, $managers, $employees);
        // Posiziono quello in alto
        posizionaQui($rowId - 1, $columnId, 'alto', $office, $developers, $managers, $employees);

        // Posiziono quello a sinistra
        posizionaQui($rowId, $columnId - 1, 'sinistra', $office, $developers, $managers, $employees);


    }
}

/*
$ultimoPosizionato = null;
for ($rowId = 0; $rowId < $height; $rowId++) {
    for ($columnId = 0; $columnId < $width; $columnId++) {
        if ($office[$rowId][$columnId] == '#' || is_object($office[$rowId][$columnId])) continue;
        else if ($office[$rowId][$columnId] == '_') {
            Log::out("Posizionando Developer in [$rowId][$columnId]", 0);
            // Developer position
            $bestDeveloper = getBestDeveloperHere($rowId, $columnId);
            if ($bestDeveloper) {
                $posizionato++;
                $employees[$bestDeveloper->id]->coordinates = [$rowId, $columnId];
                $office[$rowId][$columnId] = $bestDeveloper;
            } else Log::out("Nessun Developer trovato disponibile " . count($employees), 0);

            // $ultimoPosizionato = $bestDeveloper;
        } else if ($office[$rowId][$columnId] == 'M') {
            //Manager position
            Log::out("Posizionando Manager in [$rowId][$columnId]", 0);
            $bestManager = getBestManagerHere($rowId, $columnId);
            if ($bestManager) {
                $posizionato++;
                Log::out("Posizionato Manager ID = $bestManager->id", 0);
                $employees[$bestManager->id]->coordinates = [$rowId, $columnId];
                $managers[$bestManager->id]->coordinates = [$rowId, $columnId];
                $office[$rowId][$columnId] = $bestManager;
            } else  Log::out("Nessun Manager trovato disponibile " . count($managers), 0);

        }
    }
}
*/

$output = [];

/** @var Employee $dipendente */
foreach ($employees as $dipendente) {
    if (!empty($dipendente->coordinates)) {
        $output[] = $dipendente->coordinates[1] . ' ' . $dipendente->coordinates[0];
    } else $output[] = 'X';
}


$fileManager->output(implode(PHP_EOL, $output));