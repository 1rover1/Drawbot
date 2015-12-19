#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;
use Rover2011\AnnDroidArtist\JsonImage;

$startTime = time();

// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);


// Load an SVG document

$renderer = new JsonImage('images/dv.json');
$renderer->render($plt);



/*
// Draw the border
$plt->moveTo(0               , 0);
$plt->moveTo($plt->getWidth(), 0);
$plt->moveTo($plt->getWidth(), $plt->getHeight());
$plt->moveTo(0,                $plt->getHeight());
$plt->moveTo(0               , 0);
*/

// Output the image
//$svgDocument->render($plotterSurface);

$endTime = time();

echo "\n";
echo "Distance drawn: " . intval($plt->getDistanceDrawn()) . " (in your chosen units)\n";
echo "Distance with pen up: " . intval($plt->getDistanceTravelled() - $plt->getDistanceDrawn()) . " (in your chosen units)\n";
echo "Total distance travelled: " . intval($plt->getDistanceTravelled()) . " (in your chosen unit)\n";
echo "Time taken: " . ($endTime - $startTime) . " seconds\n";
echo "Average speed: " . ($plt->getDistanceTravelled() / ($endTime - $startTime)) . "\n";


echo "Done.\n";
