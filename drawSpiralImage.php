#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;

// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);

// The size of each "pixel" as it is on the drawn paper
// Measured in human-units
$pixelSize = 5;


// Image file
$inputFile = 'images/square.png';
$inputFile = 'images/aub.jpg';
$numColours = 3;


// Check orientation
if ($config['page']['width'] < $config['page']['height']) {
    $maxDiameter = $config['page']['height'];
} else {
    $maxDiameter = $config['page']['width'];
}

// Load image
$img = new Imagick($inputFile);
$img->quantizeImage(32, Imagick::COLORSPACE_GRAY, 0, false, false);

$newImgWidth  = intval($config['page']['width']  / $pixelSize);
$newImgHeight = intVal($config['page']['height'] / $pixelSize);

$img->resizeImage($newImgWidth, $newImgHeight, Imagick::FILTER_UNDEFINED, 1, true);
$imgWidth  = $img->getImageWidth();
$imgHeight = $img->getImageHeight();

$img->extentImage(
    $newImgWidth,
    $newImgHeight,
    -($newImgWidth - $imgWidth) / 2,
    -($newImgHeight - $imgHeight) / 2
);

$img->rotateImage(new ImagickPixel(), 180);

//$img->writeImage('output.jpg');
//die();

// Calculate some shit
$numRotations = intval($maxDiameter / $pixelSize);
$numDegrees = 360 * $numRotations;

$usedSectors = array();

$plt->moveTo($plt->getWidth(), $plt->getHeight() / 2);
for($deg = 1; $deg <= $numDegrees; $deg += 0.5) {

    // Get new x,y coordinates
    $radius = $maxDiameter / 2 * ($numDegrees - $deg) / $numDegrees;
    $x = $plt->getWidth() / 2  + cos($deg * 0.0174533) * $radius;
    $y = $plt->getHeight() / 2 + sin($deg * 0.0174533) * $radius;

    // Keep within bounds
    if ($x > $plt->getWidth()) $x = $plt->getWidth();
    if ($y > $plt->getHeight()) $y = $plt->getHeight();
    if ($x < 0) $x = 0;
    if ($y < 0) $y = 0;

    // Draw to new x,y
    $plt->drawTo($x, $y);

    // Check if we've moved into a new sector
    $sectorX = intval($x / $pixelSize);
    $sectorY = intval($y / $pixelSize);

    if (!in_array("$sectorX $sectorY", $usedSectors)) {
        // Draw sector
        $whiteness = $img->getImagePixelColor($sectorX, $sectorY)->getColor()['r'] / 255;
        if ($whiteness < 0.9) {
            // Draw an 'X'
            // Shade is represented by the size of the X
            $crossSize = $pixelSize * 0.5 * (1 - $whiteness);
            $plt->drawTo($x + $crossSize, $y + $crossSize);
            $plt->drawTo($x - $crossSize, $y - $crossSize);
            $plt->drawTo($x, $y);
            $plt->drawTo($x + $crossSize, $y - $crossSize);
            $plt->drawTo($x - $crossSize, $y + $crossSize);
            $plt->drawTo($x, $y);
        }

        // Add sector to used list
        $usedSectors[] = "$sectorX $sectorY";

    }

}

echo "Done.\n";
