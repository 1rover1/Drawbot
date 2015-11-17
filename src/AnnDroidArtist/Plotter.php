<?php

namespace Rover2011\AnnDroidArtist;

use Rover2011\AnnDroidArtist\Motor;

class Plotter
{
    // Motors
    public $leftMotor;      // Object to control left motor
    public $rightMotor;     // Object to control right motor

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
        $this->pageTop = $config['page']['top'] * $ppu;
        $this->pageLeft = $config['page']['left'] * $ppu;
        $this->pageWidth = $config['page']['width'] * $ppu;
        $this->pageHeight = $config['page']['height'] * $ppu;
        $this->pageMargin = $config['page']['margin'] * $ppu;
        $this->motorDistance = $config['motor_distance'] * $ppu;
        $this->armLengthLeft = $config['arm_length']['left'] * $ppu;
        $this->armLengthRight = $config['arm_length']['right'] * $ppu;
    }

    public function __destruct()
    {
        $this->leftMotor->reset();
        $this->rightMotor->reset();
    }

    public function getX()
    {
        $penPosition = $this->bipolarToCartesian($this->armLengthLeft, $this->armLengthRight);
        return $penPosition['x'] / $this->ppu;
    }

    public function getY()
    {
        $penPosition = $this->bipolarToCartesian($this->armLengthLeft, $this->armLengthRight);
        return $penPosition['y'] / $this->ppu;
    }

    public function drawTo($destX, $destY)
    {
        // Translate input coordinates to pips
        $destX *= $this->ppu;
        $destY *= $this->ppu;

        // Save pen start position
        $penPosition = $this->bipolarToCartesian($this->armLengthLeft, $this->armLengthRight);
        $startX = $penPosition['x'];
        $startY = $penPosition['y'];

        $distance = $this->distanceBetweenPoints($startX, $startY, $destX, $destY);

        while ($distance > 5) {
            // There are four options, in order: shorten left arm, lengthen
            // left arm, shorten right arm, lengthen right arm.
            // Translate these options to cartesian and get distance to destination
            $movementOptions = array();

            $pointShortenLeft = $this->bipolarToCartesian($this->armLengthLeft - 1, $this->armLengthRight);
            $distShortenLeft   = $this->distanceBetweenPoints($pointShortenLeft['x'], $pointShortenLeft['y'], $destX, $destY);

            $pointLengthenLeft = $this->bipolarToCartesian($this->armLengthLeft + 1, $this->armLengthRight);
            $distLengthenLeft  = $this->distanceBetweenPoints($pointLengthenLeft['x'], $pointLengthenLeft['y'], $destX, $destY);

            $pointShortenRight = $this->bipolarToCartesian($this->armLengthLeft, $this->armLengthRight - 1);
            $distShortenRight  = $this->distanceBetweenPoints($pointShortenRight['x'], $pointShortenRight['y'], $destX, $destY);

            $pointLengthenRight = $this->bipolarToCartesian($this->armLengthLeft, $this->armLengthRight + 1);
            $distLengthenRight = $this->distanceBetweenPoints($pointLengthenRight['x'], $pointLengthenRight['y'], $destX, $destY);

            // Check which option gives us the minimum distance to destination.
            // Make sure it doesn't stray too far from the line we're drawing.
            // Process accordingly
            $movementOptions = array($distShortenLeft, $distLengthenLeft, $distShortenRight, $distLengthenRight);
            rsort($movementOptions, SORT_NUMERIC);

            do {
                $minimum = array_pop($movementOptions);
                switch ($minimum) {
                    case $distShortenLeft:
                        $checkPoint = $pointShortenLeft;
                        break;
                    case $distLengthenLeft:
                        $checkPoint = $pointLengthenLeft;
                        break;
                    case $distShortenRight:
                        $checkPoint = $pointShortenRight;
                        break;
                    case $distLengthenRight:
                        $checkPoint = $pointLengthenRight;
                        break;
                    default:
                        throw new \Exception(
                            "Unable to determine minimum distance between two " .
                            "points when checking point-to-line distance."
                        );
                }

                $distanceToLive = $this->distancePointToLine(
                    $checkPoint['x'], $checkPoint['y'],
                    $destX, $destY,
                    $startX, $startY
                );
            } while ($minimum === null || $distanceToLive > 5);

            switch ($minimum) {
                case $distShortenLeft:
                    $this->leftMotor->shorten();
                    $this->armLengthLeft--;
                    break;
                case $distLengthenLeft:
                    $this->leftMotor->lengthen();
                    $this->armLengthLeft++;
                    break;
                case $distShortenRight:
                    $this->rightMotor->shorten();
                    $this->armLengthRight--;
                    break;
                case $distLengthenRight:
                    $this->rightMotor->lengthen();
                    $this->armLengthRight++;
                    break;
                default:
                    throw new \Exception(
                        "Unable to determine minimum distance between two points when turning motor."
                    );
            }

            $distance = $minimum;

            usleep(5000);   // 5ms
        }
    }

    private function distanceBetweenPoints($x1, $y1, $x2, $y2)
    {
        return sqrt(pow($y2 - $y1, 2) + pow($x2 - $x1, 2));
    }

    private function bipolarToCartesian($r1, $r2)
    {
        // formula from http://mathworld.wolfram.com/BipolarCoordinates.html

        $c = $this->motorDistance / 2;

        $x = (($r1 * $r1) - ($r2 * $r2)) / (4 * $c);
        $y = 16 * $c * $c * $r1 * $r1 - pow($r1 * $r1 - $r2 * $r2 + 4 * $c * $c, 2);
        $y = -sqrt($y) / (4 * $c);

        return array(
            'x' => $x,
            'y' => $y
        );
    }

    private function distancePointToLine($px, $py, $x1, $y1, $x2, $y2)
    {
        // formula from https://en.wikipedia.org/wiki/Distance_from_a_point_to_a_line
        // Line defined by two points

        $x0 = $px;
        $y0 = $py;

        $d = abs(($y2 - $y1) * $x0 - ($x2 - $x1) * $y0 + $x2 * $y1 - $y2 * $x1);
        $d /= sqrt(pow($y2 - $y1, 2) + pow($x2 - $x1, 2));

        return $d;
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
