#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\Drawbot\Plotter;
use Rover2011\Drawbot\Driver\SurfaceAnnDroid;
use Svg\Document;

// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);

echo "Done.\n";
