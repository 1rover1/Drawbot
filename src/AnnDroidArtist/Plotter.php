<?php

namespace Rover2011\AnnDroidArtist;

use Rover2011\AnnDroidArtist\Motor;

class Plotter
{
    // Motors
    private $leftMotor;      // Object to control left motor
    private $rightMotor;     // Object to control right motor

    private $armLengthLeft;  // Length of line between left motor and gondola
    private $armLengthRight; // Length of line between right motor and gondola

    private $motorDistance;  // Horizontal ditance between motor centres

    private $pageTop;        // Vertical distance between motor and page top
    private $pageLeft;       // Horizontal distance between left motor and page left
    private $pageWidth;      // Width of the page
    private $pageHeight;     // Height of the page
    private $pageMargin;     // Internal margin of the page (same all sides)

    private $ppu;

    public function __construct($config = null)
    {
        // set up motors
        $this->leftMotor = new Motor($config['motor_left']);
        $this->rightMotor = new Motor($config['motor_right']);
        $this->leftMotor->reset();
        $this->rightMotor->reset();

        // Pre-calculate Pips Per Unit (PPU)
        $ppu = $config['calibration']['pips'] / $config['calibration']['distance'];
        $this->ppu = $ppu;

        // Save all measurements in pips
        $this->pageTop = intval($config['page']['top'] * $ppu);
        $this->pageLeft = intval($config['page']['left'] * $ppu);
        $this->pageWidth = intval($config['page']['width'] * $ppu);
        $this->pageHeight = intval($config['page']['height'] * $ppu);
        $this->pageMargin = intval($config['page']['margin'] * $ppu);
        $this->motorDistance = intval($config['motor_distance'] * $ppu);
        $this->armLengthLeft = intval($config['arm_length']['left'] * $ppu);
        $this->armLengthRight = intval($config['arm_length']['right'] * $ppu);
    }

    public function __destruct()
    {
        $this->leftMotor->reset();
        $this->rightMotor->reset();
    }

    public function getX()
    {
        // Get current x,y pen position
        $penPosition = $this->toCartesian($this->armLengthLeft, $this->armLengthRight);

        return $penPosition['x'] / $this->ppu;
    }

    public function getY()
    {
        // Get current x,y pen position
        $penPosition = $this->toCartesian($this->armLengthLeft, $this->armLengthRight);

        return $penPosition['y'] / $this->ppu;
    }

    public function drawTo($destX, $destY)
    {
        // Translate input coordinates to pips
        $destX *= $this->ppu;
        $destY *= $this->ppu;

        // Get current x,y pen position
        $penPosition = $this->toCartesian($this->armLengthLeft, $this->armLengthRight);

        $distance = $this->getDistanceCartesian(
            $penPosition['x'],
            $penPosition['y'],
            $destX,
            $destY
        );

        while ($distance > 5) {

            // There are four options, in order: shorten left arm, lengthen
            // left arm, shorten right arm, lengthen right arm
            $armLengthLeft = $this->armLengthLeft;
            $armLengthRight = $this->armLengthRight;

            $shortenLeft   = $this->toCartesian($armLengthLeft - 1, $armLengthRight);
            $lengthenLeft  = $this->toCartesian($armLengthLeft + 1, $armLengthRight);
            $shortenRight  = $this->toCartesian($armLengthLeft, $armLengthRight - 1);
            $lengthenRight = $this->toCartesian($armLengthLeft, $armLengthRight + 1);

            // Translate these options to cartesian
            $distShortenLeft   = $this->getDistanceCartesian(
                $shortenLeft['x'],
                $shortenLeft['y'],
                $destX,
                $destY
            );
            $distLengthenLeft  = $this->getDistanceCartesian(
                $lengthenLeft['x'],
                $lengthenLeft['y'],
                $destX,
                $destY
            );
            $distShortenRight  = $this->getDistanceCartesian(
                $shortenRight['x'],
                $shortenRight['y'],
                $destX,
                $destY
            );
            $distLengthenRight = $this->getDistanceCartesian(
                $lengthenRight['x'],
                $lengthenRight['y'],
                $destX,
                $destY
            );

            // Check which option gives us the minimum distance to destination
            // Process accordingly
            $minimum = min($distShortenLeft, $distLengthenLeft, $distShortenRight, $distLengthenRight);
            switch ($minimum) {
                case $distShortenLeft:
                    //$this->leftMotor->shorten();
                    $this->armLengthLeft--;
                    echo "\noption 1";
                    break;
                case $distLengthenLeft:
                    //$this->leftMotor->lengthen();
                    $this->armLengthLeft++;
                    echo "\noption 2";
                    break;
                case $distShortenRight:
                    //$this->rightMotor->shorten();
                    $this->armLengthRight--;
                    echo "\noption 3";
                    break;
                case $distLengthenRight:
                    //$this->rightMotor->lengthen();
                    $this->armLengthRight++;
                    echo "\noption 4";
                    break;
                default:
                    echo "error\n";
            }

            $distance = $minimum;

            echo " $distance\n";

            usleep(5000);   // 5ms
        }
    }

    private function getDistanceCartesian($x1, $y1, $x2, $y2)
    {
        return sqrt(pow($y2 - $y1, 2) + pow($x2 - $x1, 2));
    }

    public function toCartesian($r1, $r2)
    {
        // formula from http://mathworld.wolfram.com/BipolarCoordinates.html

        $c = $this->motorDistance / 2;

        $x = (($r1 * $r1) - ($r2 * $r2)) / (4 * $c);
        $y = 16 * $c * $c * $r1 * $r1 - pow($r1 * $r1 - $r2 * $r2 + 4 * $c * $c, 2);
        $y = -sqrt($y) / (4 * $c);

        return array(
            'x' => intval($x),
            'y' => intval($y)
        );
    }








    public function circleLeft($radius)
    {
        $this->circle($radius, -1);
    }

    public function circleRight($radius)
    {
        $this->circle($radius, 1);
    }

    private function circle($radius, $xDirection)
    {
        $pointCount = intval($radius / 2);
        $x = array();
        $y = array();

        for($i = 0; $i < $pointCount; $i++) {
            $x[] = $xDirection * ($radius - intval($radius * cos(2 * pi() * ($i + 1) / $pointCount)));
            $y[] = intval($radius * sin(2 * pi() * ($i + 1) / $pointCount));
        }

        for($i = 0; $i < $pointCount; $i++) {
            $this->drawTo($x[$i], $y[$i]);
        }
    }

}
