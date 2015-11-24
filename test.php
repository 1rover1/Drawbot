#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;

use Rover2011\AnnDroidArtist\Driver\SurfaceAnnDroid;
use Svg\Document;

// PPU's
// 445 539
$startTime = time();

// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);


// Load an SVG document

//$fileName = 'images/nyree_stripe.svg';
$fileName = 'images/st.svg';
//$fileName = 'images/signature.svg';

$svgDocument = new Document();
$svgDocument->loadFile($fileName);

// Create a render surface for the plotter
$documentSize = $svgDocument->getDimensions();
$plotterSurface = new SurfaceAnnDroid(
    $documentSize['width'],
    $documentSize['height']
);
$plotterSurface->setPlotter($plt);


// Output the image
$svgDocument->render($plotterSurface);


$endTime = time();

echo "\n";
echo "Distance drawn: " . intval($plt->getDistanceDrawn()) . " (in your chosen units)\n";
echo "Distance with pen up: " . intval($plt->getDistanceTravelled() - $plt->getDistanceDrawn()) . " (in your chosen units)\n";
echo "Total distance travelled: " . intval($plt->getDistanceTravelled()) . " (in your chosen unit)\n";
echo "Time taken: " . ($endTime - $startTime) . " seconds\n";
echo "Average speed: " . ($plt->getDistanceTravelled() / ($endTime - $startTime)) . "\n";


echo "Done.\n";
