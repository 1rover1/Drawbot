#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;


// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);

/*
$plt->drawTo($plt->getWidth(), 0);
$plt->moveTo(0,                0);
$plt->drawTo($plt->getWidth(), $plt->getHeight());
$plt->drawTo(0,                $plt->getHeight());
$plt->drawTo(0,                0);
*/

$plt->moveTo(0, 0);
for($i = 0; $i < 10; $i++) {
    if(($i % 2) == 1) {
        $plt->drawTo($plt->getWidth * $i / 10, $plt->getHeight() - $plt->getHeight() * $i / 10);
    } else {
        $plt->drawTo($plt->getHeight() - $plt->getHeight() * $i / 10, $plt->getWidth * $i / 10);
    }
}


echo "Done.\n";
