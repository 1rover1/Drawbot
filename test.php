<?php

include('vendor/autoload.php');

//var_dump(range(2,27));
//die();

use Rover2011\AnnDroidArtist\Motor;

$config = json_decode(file_get_contents('config/config.json'), true);

$motorLeft = new Motor($config['motor_left']);
$motorRight = new Motor($config['motor_right']);

$motorLeft->reset();
$motorRight->reset();

for ($i = 0; $i<60; $i++) {
    $motorLeft->lengthen();
    $motorRight->lengthen();

    usleep(5000);
    //sleep(1);
}

$motorLeft->reset();
$motorRight->reset();

echo "Done\n";
