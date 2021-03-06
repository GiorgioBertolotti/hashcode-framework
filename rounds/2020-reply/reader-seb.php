<?php

use Utils\Log;
use Utils\FileManager;
use Utils\Stopwatch;

require_once '../../bootstrap.php';

// Classes

class Employee
{
    public $type;
    public $id;
    public $company;
    public $bonus;
    public $numSkills;
    public $skills;
    public $coordinates = [];

    public function __construct($type, $id, $company, $bonus, $skills)
    {
        $this->type = $type;
        $this->id = $id;
        $this->company = $company;
        $this->bonus = $bonus;
        $this->numSkills = count($skills);
        $this->skills = $skills;
    }
}

function calculateScore($replayer1, $replayer2)
{
    $bonus = ($replayer1->company == $replayer2->company) ? $replayer1->bonus * $replayer2->bonus : 0;
    if (get_class($replayer1) == "Developer" && get_class($replayer2) == "Developer") {
        // calc skills bonus
        $intersection = array_intersect($replayer1->skills, $replayer2->skills);
        if (count($intersection) != 0) {
            $union = array_unique(array_merge($replayer1->skills, $replayer2->skills));
            $bonus += count($intersection) * count($union);
        }
    }
    return $bonus;
}

Stopwatch::tik('Input');

// Reading the inputs
$fileManager = new FileManager($fileName);
$content = explode("\n", $fileManager->get());

list($width, $height) = explode(' ', $content[0]);

$office = [];
for ($i = 0; $i < $height; $i++) {
    $office[] = str_split($content[1 + $i]);
}

list($numDevs) = explode(' ', $content[1 + $height]);

$startingFrom = 2 + $height;
$employees = [];
$counter = 0;
$companies = [];
for ($i = 0; $i < $numDevs; $i++) {
    $devProps = explode(' ', $content[$startingFrom + $i]);
    $skills = array_splice($devProps, 3, count($devProps) - 1);
    $companies[$devProps[0]] += 1;
    $employees[] = new Employee('D', $i, $devProps[0], $devProps[1], $skills);
    $developers[] = new Employee('D', $i, $devProps[0], $devProps[1], $skills);
    $counter ++;
}

list($numProjManager) = explode(' ', $content[2 + $height + $numDevs]);

$managers = [];
$startingFrom = 3 + $height + $numDevs;
for ($i = 0; $i < $numProjManager; $i++) {
    $managerProps = explode(' ', $content[$startingFrom + $i]);
    $employees[$counter] = new Employee('M', $counter, $managerProps[0], $managerProps[1], []);
    $managers[$counter] = new Employee('M', $counter, $managerProps[0], $managerProps[1], []);
    $companies[$managerProps[0]] += 1;
    //$companies[$managerProps[0]]['counter'] += 1;
    //$companies[$managerProps[0]]['bonus'] += $devProps[1];
    $counter++;
}

$dipendentiTotali = $employees;

Log::out("Finish input reading", 0);
Stopwatch::tok('Input');
