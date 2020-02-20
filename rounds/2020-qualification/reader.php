<?php

use Utils\FileManager;
use Utils\Log;

require_once '../../bootstrap.php';

/* classes */

/* helper functions */

/* read input */

$SCORE = 0; // increment this during the solution to track the points

$fileManager = new FileManager($fileName);
$fileContent = $fileManager->get();

$fileRows = explode("\n", $fileContent);
//list(..) = explode(' ', $fileRows[0]);

Log::out("Finito di leggere input", 0, "white", "red");
