<?php

include('vendor/autoload.php');

use webignition\JsonPrettyPrinter\JsonPrettyPrinter;


echo "Creating template config file\n";

$config = json_encode([

    "motor_left" => [
        "gpio" => "6,13,19,26",
        "direction" => 1,
        "speed" => "low",
        "calibration" => [
            "pips" => 10000,
            "distance" => 539
        ]
    ],

    "motor_right" => [
        "gpio" => "24,25,8,7",
        "direction" => -1,
        "speed" => "low",
        "calibration" => [
            "pips" => 10000,
            "units" => 539
        ]
    ],

    "measurement_unit" => "mm",
    "motor_distance" => 1040,
    "page" => [
        "top" => 300,
        "left" => 170,
        "width" => 700,
        "height" => 1000
    ]

]);


$formatter = new JsonPrettyPrinter();

file_put_contents(
    'config/config.json',
    $formatter->format($config)
);

echo "Done!\n";