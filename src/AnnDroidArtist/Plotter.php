<?php

namespace Rover2011\AnnDroidArtist;

use Rover2011\AnnDroidArtist\Motor;

class Plotter
{
    // Motors
    private $leftMotor;      // Object to control left motor
    private $rightMotor;     // Object to control right motor
    private $simulate;       // Turns on/off actual movement by the plotter
    const STEP_DELAY = 5;    // Minimum time between steps (milliseconds)

    private $armLengthLeft;  // Length of line between left motor and gondola
    private $armLengthRight; // Length of line between right motor and gondola

    private $motorDistance;  // Horizontal distance between motor centres

    private $penIsUp;        // If the pen is up or not

    // Page attributes
    private $pageTop;        // Vertical distance between motor and page top
    private $pageLeft;       // Horizontal distance between left motor and page left
    private $pageWidth;      // Width of the page
    private $pageHeight;     // Height of the page
    private $pageMargin;     // Internal margin of the page (same all sides)

    // Metrics
    private $ppu;
    private $distanceTravelled;
    private $distanceDrawn;

    public function __construct($config = null)
    {
        // Set simulation status first
        $this->simulate = false;
        if (isset($config['simulate'])) {
            $this->simulate = $config['simulate'];
        }

        // set up motors
        if ($this->simulate === false) {
            $this->leftMotor = new Motor($config['motor_left']);
            $this->rightMotor = new Motor($config['motor_right']);
            $this->leftMotor->reset();
            $this->rightMotor->reset();
        }

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

        $this->distanceTravelled = 0;
        $this->distanceDrawn = 0;
        $this->penIsUp = false;
    }

    public function __destruct()
    {
        if ($this->simulate === false) {
            $this->leftMotor->reset();
            $this->rightMotor->reset();
        }
    }

    public function getDistanceTravelled()
    {
        return $this->distanceTravelled / $this->ppu;
    }

    public function getDistanceDrawn()
    {
        return $this->distanceDrawn / $this->ppu;
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

    public function getWidth()
    {
        return ($this->pageWidth - 2 * $this->pageMargin) / $this->ppu;
    }

    public function getHeight()
    {
        return ($this->pageHeight - 2 * $this->pageMargin) / $this->ppu;
    }

    public function moveTo($destX, $destY)
    {
        $this->penUp();
        $this->drawTo($destX, $destY);
        $this->penDown();
    }

    public function penUp()
    {
        // TODO implement hardware/software to lift the pen off the page
        $this->penIsUp = true;
    }

    public function penDown()
    {
        // TODO implement hardware/software to put pen back on the page
        $this->penIsUp = false;
    }

    public function drawTo($destX, $destY)
    {
        // Translate input coordinates to pips
        $destX = -($this->motorDistance / 2) + $this->pageLeft + $this->pageMargin + $destX * $this->ppu; // normal
        //$destX = -($this->motorDistance / 2) + $this->pageWidth - $this->pageMargin - $destX * $this->ppu; // flip horizontally

        //$destY = -$this->pageTop - $this->pageMargin - $destY * $this->ppu; // normal
        $destY = -$this->pageTop + $this->pageMargin - $this->pageHeight + $destY * $this->ppu; // flip vertically

        // Save pen start position
        $penPosition = $this->bipolarToCartesian($this->armLengthLeft, $this->armLengthRight);
        $startX = $penPosition['x'];
        $startY = $penPosition['y'];

        $distance = $this->distanceBetweenPoints($startX, $startY, $destX, $destY);

        // Stats
        $this->distanceTravelled += $distance;
        if ($this->penIsUp == false) $this->distanceDrawn += $distance;

        while ($distance > 3) {
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

            // Now we've got our point - process accordingly
            switch ($minimum) {
                case $distShortenLeft:
                    if ($this->simulate == false) $this->leftMotor->shorten();
                    $this->armLengthLeft--;
                    break;
                case $distLengthenLeft:
                    if ($this->simulate == false) $this->leftMotor->lengthen();
                    $this->armLengthLeft++;
                    break;
                case $distShortenRight:
                    if ($this->simulate == false) $this->rightMotor->shorten();
                    $this->armLengthRight--;
                    break;
                case $distLengthenRight:
                    if ($this->simulate == false) $this->rightMotor->lengthen();
                    $this->armLengthRight++;
                    break;
                default:
                    throw new \Exception(
                        "Unable to determine minimum distance between two points when turning motor."
                    );
            }

            $distance = $minimum;

            if ($this->simulate == false) usleep(self::STEP_DELAY * 1000);   // 5ms
        }
    }

    public function distanceBetweenPoints($x1, $y1, $x2, $y2)
    {
        return sqrt(($y2 - $y1) * ($y2 - $y1) + ($x2 - $x1) * ($x2 - $x1));
    }

    private function bipolarToCartesian($r1, $r2)
    {
        // formula from http://mathworld.wolfram.com/BipolarCoordinates.html

        $c = $this->motorDistance / 2;

        $x = (($r1 * $r1) - ($r2 * $r2)) * 0.25 / $c;
        $y = 16 * $c * $c * $r1 * $r1 - ($r1 * $r1 - $r2 * $r2 + 4 * $c * $c) * ($r1 * $r1 - $r2 * $r2 + 4 * $c * $c);
        $y = -sqrt($y) * 0.25 / $c;

        return array(
            'x' => $x,
            'y' => $y
        );
    }

    private function distancePointToLine($px, $py, $x1, $y1, $x2, $y2)
    {
        // formula from https://en.wikipedia.org/wiki/Distance_from_a_point_to_a_line
        // Line defined by two points

        $d = ($y2 - $y1) * $px - ($x2 - $x1) * $py + $x2 * $y1 - $y2 * $x1;
        if ($d < 0) $d *= -1;
        $d /= sqrt(($y2 - $y1) * ($y2 - $y1) + ($x2 - $x1) * ($x2 - $x1));

        return $d;
    }

    /*
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
    */
}
