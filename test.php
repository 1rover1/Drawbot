#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;
use Rover2011\AnnDroidArtist\JsonImage;

use webignition\JsonPrettyPrinter\JsonPrettyPrinter;

$fileName = 'images/yoda.json';

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
    $endTime = microtime(true);

    echo "Optimisation time: " . ($endTime - $startTime) . " seconds\n";
}

die();
// Render the document

$startTime = microtime(true);
$output->render();
$endTime = microtime(true);


$x = $plt->getConfig();
$y = json_encode($x);
$z = new JsonPrettyPrinter();
var_dump($z->format($y));

// Output stats for this job

echo "Distance drawn: " . intval($plt->getDistanceDrawn()) . " (in your chosen units)\n";
echo "Distance with pen up: " . intval($plt->getDistanceTravelled() - $plt->getDistanceDrawn()) . " (in your chosen units)\n";
echo "Total distance travelled: " . intval($plt->getDistanceTravelled()) . " (in your chosen unit)\n";
echo "Time taken: " . ($endTime - $startTime) . " seconds\n";
echo "Average speed: " . ($plt->getDistanceTravelled() / ($endTime - $startTime)) . "\n";


echo "Done.\n";
