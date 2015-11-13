<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;

$config = json_decode(file_get_contents('config/config.json'), true);

$plt = new Plotter($config);


//$plt->penTo(1000,-1000);

$plt->circleRight(600);
$plt->circleLeft(400);

echo "Done\n";
