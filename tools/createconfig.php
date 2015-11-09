<?php

include('vendor/autoload.php');

use webignition\JsonPrettyPrinter\JsonPrettyPrinter;


echo "Creating template config file\n";

$config = json_encode([
    "motor_left" => [
        "gpio" => "1,2,3,4",
        "direction" => 1,
        "speed" => "low",
        "calibration" => [
            "pips" => 10000,
            "distance" => 539
        ]
    ],

    "motor_right" => [
        "gpio" => "1,2,3,4",
        "direction" => 1,
        "speed" => "low",
        "calibration" => [
            "pips" => 10000,
            "units" => 539
        ]
    ],

    "motor_distance" => 990,
    "page" => [
        "top" => 300,
        "left" => 150,
        "width" => 550,
        "height" => 750
    ],
    "measurement_unit" => "mm"

]);


$formatter = new JsonPrettyPrinter();

file_put_contents(
    'config/config.json',
    $formatter->format($config)
);

echo "Done!\n";
