<?php

use Utils\Log;

error_reporting(E_ALL);

require_once '../../bootstrap.php';

$fileName = 'a';

include 'reader-seb.php';

/** Stuff... */

/**
 * @param Employee $employeStart
 * @return mixed|null
 */
function getBestEmployeBySkills($employeStart)
{
    global $employees;

    if (is_object($employeStart)) {
        $bestScore = 0;
        $bestEmployeeByTop = null;
        foreach ($employees as $employee) {
            $skillsComuni = count(array_intersect($employee->skills, $employeStart->skills));
            $skillsDiverse = max(count($employee->skills), count($employeStart->skills)) - $skillsComuni;
            if ($skillsComuni == count($employeStart->skills) && count($employee->skills) == count($employeStart->skills)) {
                //le skills di entrambi i dipendenti sono identifici score = 0
                continue;
            } else if ($bestScore == 0 || $bestScore < ($skillsComuni * $skillsDiverse)) {
                $bestScore = ($skillsComuni * $skillsDiverse);
                $bestEmployeeByTop = $employee;
            }
        }

        return ['employee' => $bestEmployeeByTop, 'localScore' => $bestScore];
    } else
        return ['score' => 0];
}


function getBestDeveloperHere($rowId, $columnId)
{
    global $employees, $office, $positions;

    // nessuna posizione ancora assegnata prendo il primo developer
    if (empty($positions)) {
        foreach ($employees as $employee) {
            if ($employee->type = 'D') return $employee;
        }
    } else {
        $topEmploye = $office[$rowId][$columnId - 1];
        $bestEmployeByTop = getBestEmployeBySkills($topEmploye);

        $rightEmploye = $office[$rowId + 1][$columnId];
        $bestEmployeByRight = getBestEmployeBySkills($rightEmploye);


        $bottomEmploye = $office[$rowId][$columnId + 1];
        $bestEmployeByBottom = getBestEmployeBySkills($bottomEmploye);

        $leftEmployee = $office[$rowId - 1][$columnId];
        $bestEmployeByLeft = getBestEmployeBySkills($leftEmployee);

        $bestScore = max([$bestEmployeByTop['localScore'], $bestEmployeByRight['localScore'], $bestEmployeByBottom['localScore'], $bestEmployeByLeft['localScore']]);

        if($bestEmployeByTop['localScore'] && $bestEmployeByTop['localScore'] == $bestScore) return $bestEmployeByTop['employee'];
        if($bestEmployeByRight['localScore'] && $bestEmployeByRight['localScore'] == $bestScore) return $bestEmployeByRight['employee'];
        if($bestEmployeByBottom['localScore'] && $bestEmployeByBottom['localScore'] == $bestScore) return $bestEmployeByBottom['employee'];
        if($bestEmployeByLeft['localScore'] && $bestEmployeByLeft['localScore'] == $bestScore) return $bestEmployeByLeft['employee'];
        else {
            return array_values($employees)[0];
        }
    }
}

function array_search_value($search_value, $array, $id_path)
{

    if (is_array($array) && count($array) > 0) {

        foreach ($array as $key => $value) {

            $temp_path = $id_path;

            // Adding current key to search path
            array_push($temp_path, $key);

            // Check if this value is an array
            // with atleast one element
            if (is_array($value) && count($value) > 0) {
                $res_path = array_search_id(
                    $search_value, $value, $temp_path);

                if ($res_path != null) {
                    return $res_path;
                }
            } else if ($value == $search_value) {
                return join(" --> ", $temp_path);
            }
        }
    }

    return null;
}

function getBestManagerHere($rowId, $columnId)
{
    global $managers, $office;

    $topEmploye = $office[$rowId - 1][$columnId];
    $rightEmploye = $office[$rowId][$columnId + 1];
    $bottomEmploye = $office[$rowId + 1][$columnId];
    $leftEmployee = $office[$rowId][$columnId - 1];

    $arrayCompanies = [
        is_object($topEmploye) && $topEmploye->type == 'D' ? $topEmploye->company : null,
        is_object($rightEmploye) && $rightEmploye->type == 'D' ? $rightEmploye->company : null,
        is_object($bottomEmploye) && $bottomEmploye->type == 'D' ? $bottomEmploye->company : null,
        is_object($leftEmployee) && $leftEmployee->type == 'D' ? $leftEmployee->company : null
    ];

    $companies = array_count_values($arrayCompanies);
    arsort($companies);
    $mostPopularCompany = array_slice(array_keys($companies), 0, 1, true)[0];

    $bestManager = null;
    foreach ($managers as $manager) {
        if($mostPopularCompany) {
            if ($manager->company == $mostPopularCompany) {
                $bestManager = $manager;
                break;
            }
        }
        else {
            $bestManager = $manager;
            break;
        }
    }

    return $bestManager;

}

$positions = [];

function cmp($a, $b)
{
    if ($a->bonus == $b->bonus) {
        return 0;
    }
    return ($a->bonus > $b->bonus) ? -1 : 1;
}

usort($managers, "cmp");

$output = [];

for ($rowId = 0; $rowId < $height; $rowId++) {
    for ($columnId = 0; $columnId < $width; $columnId++) {
        if ($office[$rowId][$columnId] == '#' || is_object($office[$rowId][$columnId])) continue;
        else if ($office[$rowId][$columnId] == '_') {
            Log::out("Posizionando Developer in [$rowId][$columnId]", 0);
            // Developer position
            $bestDeveloper = getBestDeveloperHere($rowId, $columnId);
            if($bestDeveloper) {
                // tolgo dai dipendenti diponibili quello che ho appena messo a lavorare
                unset($employees[$bestDeveloper->id]);

                $bestDeveloper->coordinates = [$columnId, $rowId];

                $dipendentiTotali[$bestDeveloper->id] = $bestDeveloper;

                //posiziono il developer
                Log::out("Posizionato Developer ID = $bestDeveloper->id", 0);
                $positions[$bestDeveloper->id] = [$rowId, $columnId];
                $office[$rowId][$columnId] = $bestDeveloper;
            }
            else Log::out("Nessun Developer trovato disponibile " . count($employees), 0);

        } else if ($office[$rowId][$columnId] == 'M') {
            //Manager position
            Log::out("Posizionando Manager in [$rowId][$columnId]", 0);
            $bestManager = getBestManagerHere($rowId, $columnId);
            if($bestManager) {
                unset($employees[$bestManager->id]);
                Log::out("Posizionato Manager ID = $bestManager->id", 0);

                $positions[$bestManager->id] = [$rowId, $columnId];
                $office[$rowId][$columnId] = $bestManager;

                $bestManager->coordinates = [$columnId, $rowId];

                $dipendentiTotali[$bestManager->id] = $bestManager;
            }
            else  Log::out("Nessun Manager trovato disponibile " . count($managers), 0);
        }
    }
}

$output = [];

$lastKey = max(array_keys($positions));
for($i = 0; $i < $lastKey; $i++)
{
    if(!empty($positions[$i])) {
        $output[] = $positions[$i][1] . ' ' . $positions[$i][0];
    }
    else $output[] = 'X';
}


$fileManager->output(implode(PHP_EOL, $output));