<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Motor;

$config = json_decode(file_get_contents('config/config.json'), true);

$motorLeft = new Motor($config['motor_left']);
$motorRight = new Motor($config['motor_right']);

echo "Done\n";
