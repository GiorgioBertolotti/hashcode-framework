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

    public function __construct($id, $company, $bonus, $skills)
    {
        $this->id = $id;
        $this->company = $company;
        $this->bonus = $bonus;
        $this->numSkills = count($skills);
        $this->skills = $skills;
    }
}

class ProjectManager
{
    public $id;
    public $company;
    public $bonus;

    public function __construct($id, $company, $bonus)
    {
        $this->id = $id;
        $this->company = $company;
        $this->bonus = $bonus;
    }
}

Stopwatch::tik('Input');

// Reading the inputs
$fileManager = new FileManager($fileName);
$content = explode("\n", $fileManager->get());

list($width, $height) = explode(' ', $content[0]);

$office = [];
foreach ($content as $rowNumber => $row) {
    if ($rowNumber > 0) {
        $office[] = explode(' ', $row);
    }
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
    $managers[] = new ProjectManager($i, $devProps[0], $devProps[1]);
}

Log::out("Finish input reading", 0);
Stopwatch::tok('Input');
