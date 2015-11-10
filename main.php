<?php

include('vendor/autoload.php');

use timesplinter\gphpio\GPIO;
use timesplinter\gphpio\RPi;

$model = new RPi();
$gpio = new GPIO($model);
$pin = 17;

if($gpio->isExported($pin) === false)
    $gpio->export($pin, GPIO::MODE_OUTPUT);

echo 'This is a ' , $model->getName() , PHP_EOL;

for($i = 0; $i < 10; ++$i) {
    $gpio->write($pin, 1);
    echo 'The pin is now: ' , $gpio->read($pin) , PHP_EOL;
    sleep(1);

    $gpio->write($pin, 0);
    echo 'The pin is now: ' , $gpio->read($pin) , PHP_EOL;
    sleep(1);
}

$gpio->unexport($pin);
