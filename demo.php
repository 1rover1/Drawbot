#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;


// Create a plotter
$config = json_decode(file_get_contents('config/config.json'), true);
$plt = new Plotter($config);


$numSquares = 20;

for($squareCounter = 0; $squareCounter < $numSquares; $squareCounter++) {

    $portion = $squareCounter / $numSquares;

    echo "$portion\n";

    $plt->moveTo(
        $plt->getWidth() * $portion,
        0
    );

    $plt->drawTo(
        0,
        $plt->getHeight() * (1 - $portion)
    );

    $plt->drawTo(
        $plt->getWidth() * (1 - $portion),
        $plt->getHeight()
    );

    $plt->drawTo(
        $plt->getWidth(),
        $plt->getHeight() * $portion
    );

    $plt->drawTo(
        $plt->getWidth() * $portion,
        0
    );
}




echo "Done.\n";
