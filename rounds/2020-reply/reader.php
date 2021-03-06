<?php

use Utils\Log;
use Utils\FileManager;
use Utils\Stopwatch;

require_once '../../bootstrap.php';

// Classes

class Developer
{
    public $id;
    public $company;
    public $bonus;
    public $numSkills;
    public $skills;
    public $placed;
    public $r;
    public $c;

    public function __construct($id, $company, $bonus, $skills)
    {
        $this->id = $id;
        $this->company = $company;
        $this->bonus = $bonus;
        $this->numSkills = count($skills);
        $this->skills = $skills;
        $this->placed = false;
        $this->r = null;
        $this->c = null;
    }
}

class ProjectManager
{
    public $id;
    public $company;
    public $bonus;
    public $placed;
    public $r;
    public $c;

    public function __construct($id, $company, $bonus)
    {
        $this->id = $id;
        $this->company = $company;
        $this->bonus = $bonus;
        $this->placed = false;
        $this->r = null;
        $this->c = null;
    }
}

function calculateBonus($replayer1, $replayer2)
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
$developers = [];
for ($i = 0; $i < $numDevs; $i++) {
    $devProps = explode(' ', $content[$startingFrom + $i]);
    $skills = array_splice($devProps, 3, count($devProps) - 1);
    $developers[] = new Developer($i, $devProps[0], $devProps[1], $skills);
}

list($numProjManager) = explode(' ', $content[2 + $height + $numDevs]);

$startingFrom = 3 + $height + $numDevs;
$managers = [];
for ($i = 0; $i < $numProjManager; $i++) {
    $managerProps = explode(' ', $content[$startingFrom + $i]);
    $managers[] = new ProjectManager($i, $managerProps[0], $managerProps[1]);
}

Log::out("Finish input reading", 0);
Stopwatch::tok('Input');
