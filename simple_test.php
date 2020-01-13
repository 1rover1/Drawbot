#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;
use Rover2011\AnnDroidArtist\Driver\SurfaceAnnDroid;
use Svg\Document;

// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);

$plt->drawTo(0, 0);
$plt->drawTo($plt->getWidth(), 0);
$plt->drawTo($plt->getWidth(), $plt->getHeight());
$plt->drawTo(0, $plt->getHeight());
$plt->drawTo(0, 0);

echo "Done.\n";
