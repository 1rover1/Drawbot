#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;


// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);




for($i = 1; $i <= 10; $i++) {
    if(($i % 2) == 1) {
        $plt->moveTo($plt->getWidth * $i / 10, 0);
        $plt->drawTo(0, $plt->getHeight() - $plt->getHeight() * $i / 10);
    } else {
        $plt->moveTo(0, $plt->getHeight() - $plt->getHeight() * $i / 10);
        $plt->drawTo($plt->getWidth * $i / 10, 0);
    }
}




echo "Done.\n";
