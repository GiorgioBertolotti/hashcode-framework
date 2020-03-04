<?php

use Utils\Stopwatch;
use Utils\Log;
use Utils\Visual\Colors;
use Utils\Visual\VisualStandard;

//IL B NON E POSSIBILE RISOLVERLO! LO SI VEDE CHIARO DA VISUAL
$fileName = 'd';

require_once 'reader.php';

error_reporting(E_ALL);


$visual = new VisualStandard( abs($minCol) + $maxCol, abs($minRow) + $maxRow);
$R=abs($minRow) + $maxRow;
$C=abs($minCol) + $maxCol;


foreach ($ostacoli as $ostacolo) {
   /* $visual->setLine($ostacolo[0][1] < 0 ? $R - $maxRow - abs($ostacolo[0][1]) : $ostacolo[0][1] + abs($minRow),
        $ostacolo[0][0] < 0 ? $C - $maxCol - abs($ostacolo[0][0]) : $ostacolo[0][0] + abs($minCol),
        $ostacolo[1][1] < 0 ? $R - $maxRow - abs($ostacolo[1][1]) : $ostacolo[1][1] + abs($minRow),
        $ostacolo[1][0] < 0 ? $C - $maxCol - abs($ostacolo[1][0]) : $ostacolo[1][0] + abs($minCol),
        Colors::black);
    $visual->setLine($ostacolo[2][1] < 0 ? $R - $maxRow - abs($ostacolo[2][1]) : $ostacolo[2][1] + abs($minRow),
        $ostacolo[2][0] < 0 ? $C - $maxCol - abs($ostacolo[2][0]) : $ostacolo[2][0] + abs($minCol),
        $ostacolo[1][1] < 0 ? $R - $maxRow - abs($ostacolo[1][1]) : $ostacolo[1][1] + abs($minRow),
        $ostacolo[1][0] < 0 ? $C - $maxCol - abs($ostacolo[1][0]) : $ostacolo[1][0] + abs($minCol),
        Colors::black);
    $visual->setLine($ostacolo[0][1] < 0 ? $R - $maxRow - abs($ostacolo[0][1]) : $ostacolo[0][1] + abs($minRow),
        $ostacolo[0][0] < 0 ? $C - $maxCol - abs($ostacolo[0][0]) : $ostacolo[0][0] + abs($minCol),
        $ostacolo[2][1] < 0 ? $R - $maxRow - abs($ostacolo[2][1]) : $ostacolo[2][1] + abs($minRow),
        $ostacolo[2][0] < 0 ? $C - $maxCol - abs($ostacolo[2][0]) : $ostacolo[2][0] + abs($minCol),
        Colors::black);*/

    $points = [
        $ostacolo[0][1] < 0 ? $R - $maxRow - abs($ostacolo[0][1]) : $ostacolo[0][1] + abs($minRow),
        $ostacolo[0][0] < 0 ? $C - $maxCol - abs($ostacolo[0][0]) : $ostacolo[0][0] + abs($minCol),
        $ostacolo[1][1] < 0 ? $R - $maxRow - abs($ostacolo[1][1]) : $ostacolo[1][1] + abs($minRow),
        $ostacolo[1][0] < 0 ? $C - $maxCol - abs($ostacolo[1][0]) : $ostacolo[1][0] + abs($minCol),
        $ostacolo[2][1] < 0 ? $R - $maxRow - abs($ostacolo[2][1]) : $ostacolo[2][1] + abs($minRow),
        $ostacolo[2][0] < 0 ? $C - $maxCol - abs($ostacolo[2][0]) : $ostacolo[2][0] + abs($minCol),
    ];

    $visual->setBgPoligon($points, Colors::black);

    // $visual->setPixel($ostacolo[0][1] < 0 ? $R - $maxRow - abs($ostacolo[0][1]) : $ostacolo[0][1] + abs($minRow) , $ostacolo[0][0] < 0 ? $C - $maxCol - abs($ostacolo[0][0]) : $ostacolo[0][0] + abs($minCol), Colors::brown4);
    // $visual->setPixel($ostacolo[1][1] < 0 ? $R - $maxRow - abs($ostacolo[1][1]) : $ostacolo[1][1] + abs($minRow) , $ostacolo[1][0] < 0 ? $C - $maxCol - abs($ostacolo[1][0]) : $ostacolo[1][0] + abs($minCol), Colors::brown4);
    // $visual->setPixel($ostacolo[1][1] < 0 ? $R - $maxRow - abs($ostacolo[1][1]) : $ostacolo[1][1] + abs($minRow) , $ostacolo[1][0] < 0 ? $C - $maxCol - abs($ostacolo[1][0]) : $ostacolo[1][0] + abs($minCol), Colors::brown4);
    // $visual->setPixel($ostacolo[2][1] < 0 ? $R - $maxRow - abs($ostacolo[2][1]) : $ostacolo[2][1] + abs($minRow) , $ostacolo[2][0] < 0 ? $C - $maxCol - abs($ostacolo[2][0]) : $ostacolo[2][0] + abs($minCol), Colors::brown4);
}

$visual->setLine($startingPoints[1] < 0 ? $R - $maxRow - abs($startingPoints[1]) : $startingPoints[1] + abs($minRow),
    $startingPoints[0] < 0 ? $C - $maxCol - abs($startingPoints[0]) : $startingPoints[0] + abs($minCol),
    $endingPoints[1] < 0 ? $R - $maxRow - abs($endingPoints[1]) : $endingPoints[1] + abs($minRow),
    $endingPoints[0]< 0 ? $C - $maxCol - abs($endingPoints[0]) : $endingPoints[0] + abs($minCol),
    Colors::red6);


$visual->save($fileName);

