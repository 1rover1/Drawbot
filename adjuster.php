#!/usr/bin/php
<?php

include('vendor/autoload.php');

use Rover2011\AnnDroidArtist\Motor;

$config = json_decode(file_get_contents('config/config.json'), true);

$motor = new Motor($config['motor_left']);
//$motor = new Motor($config['motor_right']);

for ($i = 0; $i < 1000; $i++) {
    $motor->lengthen();
    usleep (5000);
}
