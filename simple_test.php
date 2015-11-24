#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;

use Rover2011\AnnDroidArtist\Driver\SurfaceAnnDroid;
use Svg\Document;

// PPU's
// 445 539


// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);


for ($i = 0; $i < 10000; $i++) {
    $plt->rightMotor->shorten();
    usleep (5000);
}

echo "Done.\n";
