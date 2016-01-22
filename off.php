#!/usr/bin/php
<?php

include('vendor/autoload.php');

use timesplinter\gphpio\RPi;
use timesplinter\gphpio\GPIO;

$model = new RPi();
$gpio = new GPIO($model);

echo $model->getName();

die();

use Rover2011\AnnDroidArtist\Plotter;
use Rover2011\AnnDroidArtist\Driver\SurfaceAnnDroid;
use Svg\Document;


// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);

echo "Done.\n";
