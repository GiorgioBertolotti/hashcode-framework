<?php

use Utils\FileManager;
use Utils\Log;

require_once '../../bootstrap.php';

// classes

// reader

$fileManager = new FileManager($fileName);
$fileContent = $fileManager->get();

$fileRows = explode("\n", $fileContent);
//list($numBooks, $numLibraries, $numDays) = explode(' ', $fileRows[0]);

Log::out("Finished input reading", 0);
