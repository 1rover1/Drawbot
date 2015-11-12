<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Plotter;

$config = json_decode(file_get_contents('config/config.json'), true);

$plt = new Plotter($config);


//$plt->penTo(1000,-1000);

$radius = 2000;
$pointCount = intval($radius / 2);
$x = array();
$y = array();

for($i = 0; $i < $pointCount; $i++) {
    $x[] = intval($radius * cos(2 * pi() * ($i + 1) / $pointCount)) - $radius;
    $y[] = intval($radius * sin(2 * pi() * ($i + 1) / $pointCount));
}

for($i = 0; $i < $pointCount; $i++) {
    $plt->drawTo($x[$i], $y[$i]);
}

echo "Done\n";
