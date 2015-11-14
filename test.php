#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;

$config = json_decode(file_get_contents('config/config.json'), true);

$plt = new Plotter($config);

$currentX = $plt->getX();
$currentY = $plt->getY();

$plt->drawTo($currentX - 100, $currentY);


//var_dump(bipolarToCartesian(112, 112));
