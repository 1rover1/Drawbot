#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;

// Demo
// ====
//
// Draw a spiral

// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);

$numRotations = 35;
$numDegrees = 360 * $numRotations;

$plt->moveTo($plt->getWidth(), $plt->getHeight() / 2);
for($deg = 1; $deg <= $numDegrees; $deg++) {
    $radius = $plt->getWidth() / 2 * ($numDegrees - $deg) / $numDegrees;
    $x = cos($deg * 0.0174533) * $radius;
    $y = sin($deg * 0.0174533) * $radius;
    $plt->drawTo($plt->getWidth() / 2 + $x, $plt->getHeight() / 2 + $y);
}

echo "Done.\n";
