#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;

echo "Start: " . date("r") . "\n";

// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);

// The size of each "pixel" as it is on the drawn paper
// Measured in human-units
$pixelSize = 3;


// Image file
$inputFile = 'images/kerry.jpg';

// Check orientation
if ($config['page']['width'] < $config['page']['height']) {
    $maxDiameter = $config['page']['height'];
} else {
    $maxDiameter = $config['page']['width'];
}

// Load image
$startTime = microtime(true);
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
echo "Loaded image $inputFile in " . round(microtime(true) - $startTime, 3) . " seconds\n";


// Calculate list of points to draw
$imageType = "vert-stripes";
$pointList = array();
$startTime = microtime(true);

switch($imageType) {
    case "spiral":
        $numRotations = intval($maxDiameter / $pixelSize);
        $numDegrees = 360 * $numRotations;

        $pointList[] = array($plt->getWidth(), $plt->getHeight() / 2);
        for($deg = 1; $deg <= $numDegrees; $deg += 0.5) {
            $radius = $maxDiameter / 2 * ($numDegrees - $deg) / $numDegrees;
            $x = $plt->getWidth() / 2  + cos($deg * 0.0174533) * $radius;
            $y = $plt->getHeight() / 2 + sin($deg * 0.0174533) * $radius;

            // Keep within bounds
            if ($x > $plt->getWidth()) $x = $plt->getWidth();
            if ($y > $plt->getHeight()) $y = $plt->getHeight();
            if ($x < 0) $x = 0;
            if ($y < 0) $y = 0;

            $pointList[] = array($x, $y);
        }
        break;

    case "vert-stripes":
        $drawDirection = 1;

        $pointList[] = array(0, 0);
        for($x = 0; $x <= $plt->getWidth(); $x += $pixelSize) {
            for($y = 0; $y <= $plt->getHeight(); $y += $pixelSize) {
                $pointList[] = array(
                    $x,
                    ($drawDirection == 1 ? $y: $plt->getHeight() - $y)
                );
            }
            $drawDirection = 1 - $drawDirection;
        }
        break;
}
echo "Generated $imageType point list in " . round(microtime(true) - $startTime, 3) . " seconds\n";

$usedSectors = array();

$plt->moveTo($pointList[0][0], $pointList[0][1]);
array_shift($pointList);

$startTime = microtime(true);
foreach($pointList as $point) {

    $x = $point[0];
    $y = $point[1];

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
echo "Image drawn in " . round(microtime(true) - $startTime, 3) . " seconds\n";
echo "Finish: " . date("r") . "\n";
echo "Done.\n";
