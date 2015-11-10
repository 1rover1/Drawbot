<?php

namespace Rover2011\AnnDroidArtist;

class Motor
{
    // Basic motor attributes
    private $gpio;           // Comma separated list of 4 GPIO pins
    private $direction;      // 1 or -1 to indicate CW or CCW
    private $speed;          // high or low

    private $stepSequence;

    public function __construct($config = null)
    {
        if (isset($config['direction'])) {
            $this->setDirection($config['direction']);
        }

        if (isset($config['gpio'])) {
            $this->setGpio($config['gpio']);
        }

        if (isset($config['speed'])) {
            $this->setSpeed($config['speed']);
        }
    }

    public function setDirection($value)
    {
        if ($value === 'cw' || $value === 'ccw') {
            $this->direction = $value;
        } else {
            throw new \InvalidArgumentException('Direction needs to be cw or ccw');
        }
    }

    public function setGpio($value)
    {
        $regex = '/\d{1,2},\d{1,2},\d{1,2},\d{1,2}/';

        if (preg_match($regex, $value)) {
            $this->gpio = $value;
        } else {
            throw new \InvalidArgumentException(
                'GPIO pins need to be provided as a comma separated list'
            );
        }
    }

    public function setSpeed($value)
    {
        if ($value === 'high' || $value === 'low') {
            $this->speed = $value;
        } else {
            throw new \InvalidArgumentException('Speed value of high or low expected.');
        }
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function getGpio()
    {
        return $this->gpio;
    }

    public function getSpeed()
    {
        return $this->speed;
    }

    public function shorten()
    {
        // pull the pen closer to the Motor

    }

    public function lengthen()
    {
        // push the pen farther away
    }
}
