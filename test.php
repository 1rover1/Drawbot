#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;


$config = json_decode(file_get_contents('config/config.json'), true);

$plt = new Plotter($config);

// Draw 100mm square
$size = 150;

$plt->drawTo($plt->getX()-$size, $plt->getY());
$plt->drawTo($plt->getX(), $plt->getY()+$size);
$plt->drawTo($plt->getX()+$size, $plt->getY());
$plt->drawTo($plt->getX(), $plt->getY()-$size);


// 445 539
