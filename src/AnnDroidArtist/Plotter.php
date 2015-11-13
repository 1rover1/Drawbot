<?php

namespace Rover2011\AnnDroidArtist;

use Rover2011\AnnDroidArtist\Motor;

class Plotter
{
    // Motors
    private $left;
    private $right;

    // Pen position
    private $penX;
    private $penY;

    public function __construct($config = null)
    {
        // set up motors
        $this->left = new Motor($config['motor_left']);
        $this->right = new Motor($config['motor_right']);
        $this->left->reset();
        $this->right->reset();

        $this->penX = 0;
        $this->penY = 0;
    }

    public function __destruct()
    {
        $this->left->reset();
        $this->right->reset();
    }

    public function penTo($x, $y)
    {
        // @TODO
        // In the future we'll want to add another motor
        // which can lift the pen off the page physically.
        // $this->penUp();

        $this->drawTo($x, $y);

        // $this->penDown();
    }

    public function drawTo($destX, $destY)
    {
        $distance = $this->getDistance($this->penX, $this->penY, $destX, $destY);

        while ($distance > 2) {
            $distLeft   = $this->getDistance($this->penX - 1, $this->penY, $destX, $destY);
            $distRight  = $this->getDistance($this->penX + 1, $this->penY, $destX, $destY);
            $distTop    = $this->getDistance($this->penX, $this->penY + 1, $destX, $destY);
            $distBottom = $this->getDistance($this->penX, $this->penY - 1, $destX, $destY);

            $minimum = min($distLeft, $distRight, $distTop, $distBottom);

            switch ($minimum) {
                case $distLeft:
                    $this->left->shorten();
                    $this->penX--;
                    break;
                case $distRight:
                    $this->left->lengthen();
                    $this->penX++;
                    break;
                case $distTop:
                    $this->right->shorten();
                    $this->penY++;
                    break;
                case $distBottom:
                    $this->right->lengthen();
                    $this->penY--;
                    break;
                default:
                    echo "error\n";
            }

            $distance = $minimum;

            usleep(5000);   // 5ms
        }
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

    private function getDistance($x1, $y1, $x2, $y2)
    {
        return sqrt(pow($y2 - $y1, 2) + pow($x2 - $x1, 2));
    }

    //private bipolarXToCartestian
}
