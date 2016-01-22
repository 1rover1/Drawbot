#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;
use Rover2011\AnnDroidArtist\JsonImage;
//use webignition\JsonPrettyPrinter\JsonPrettyPrinter;

$fileName = 'images/dv.json';

// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);

echo "Date: " . date("r") . "\n";
echo "Filename: $fileName\n";

// Load a document

$output = new JsonImage($fileName, $plt);
echo "Line count: " . $output->getLineCount() . "\n";
echo "Point count: " . $output->getPointCount() . "\n";

if (true) {
    $startTime = microtime(true);
    $output->optimise();
    $timeTaken = microtime(true) - $startTime;

    echo "Optimisation time: " . round($timeTaken, 3) . " seconds\n";

    if ($timeTaken > 60) {
        readline ("Hit ENTER when you're ready.");
    }
}

// Render the document

$startTime = microtime(true);
$output->render();
$endTime = microtime(true);

/*
$x = $plt->getConfig();
$y = json_encode($x);
$z = new JsonPrettyPrinter();
var_dump($z->format($y));
*/

// Output stats for this job
echo "Distance drawn: " . intval($plt->getDistanceDrawn()) . " (in your chosen units)\n";
echo "Distance with pen up: " . intval($plt->getDistanceTravelled() - $plt->getDistanceDrawn()) . " (in your chosen units)\n";
echo "Total distance travelled: " . intval($plt->getDistanceTravelled()) . " (in your chosen unit)\n";
echo "Time taken: " . round($endTime - $startTime, 3) . " seconds\n";
echo "Average speed: " . round($plt->getDistanceTravelled() / ($endTime - $startTime), 3) . "\n";

echo "Done.\n";
