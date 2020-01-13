<?php

namespace Rover2011\Drawbot\Driver;

use Rover2011\Drawbot\Plotter;
use Svg\Style;
use Svg\Surface\SurfaceInterface;

class SurfaceAnnDroid implements SurfaceInterface
{
    private $width;
    private $height;

    private $plotter;
    private $ratio;
    private $offsetX;
    private $offsetY;
    private $debug;


    /** @var Style */
    private $style;

    public function __construct($w, $h)
    {
        $this->width = $w;
        $this->height = $h;

        $this->debug = true;
    }

    function out()
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
        return '';
    }

    public function save()
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function restore()
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function scale($x, $y)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function rotate($angle)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function translate($x, $y)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function transform($a, $b, $c, $d, $e, $f)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function beginPath()
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function closePath()
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function fillStroke()
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function clip()
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function fillText($text, $x, $y, $maxWidth = null)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function strokeText($text, $x, $y, $maxWidth = null)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function drawImage($image, $sx, $sy, $sw = null, $sh = null, $dx = null, $dy = null, $dw = null, $dh = null)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function lineTo($x, $y)
    {
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";

        list ($pltX, $pltY) = $this->translateToPage($x, $y);
        $this->plotter->drawTo($pltX, $pltY);
    }

    public function moveTo($x, $y)
    {
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";

        list ($pltX, $pltY) = $this->translateToPage($x, $y);
        $this->plotter->moveTo($pltX, $pltY);
    }

    public function quadraticCurveTo($cpx, $cpy, $x, $y)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function bezierCurveTo($cp1x, $cp1y, $cp2x, $cp2y, $x, $y)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " ";

        // TODO change to an actual bezier

        echo "($cp1x, $cp1y)";
        list ($pltX, $pltY) = $this->translateToPage($cp1x, $cp1y);
        $this->plotter->drawTo($pltX, $pltY);

        echo ", ($cp2x, $cp2y)";
        list ($pltX, $pltY) = $this->translateToPage($cp2x, $cp2y);
        $this->plotter->drawTo($pltX, $pltY);

        echo ", ($x, $y)";
        list ($pltX, $pltY) = $this->translateToPage($x, $y);
        $this->plotter->drawTo($pltX, $pltY);

        echo "\n";
    }

    public function arcTo($x1, $y1, $x2, $y2, $radius)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function arc($x, $y, $radius, $startAngle, $endAngle, $anticlockwise = false)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function circle($x, $y, $radius)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function ellipse($x, $y, $radiusX, $radiusY, $rotation, $startAngle, $endAngle, $anticlockwise)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function fillRect($x, $y, $w, $h)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function rect($x, $y, $w, $h, $rx = 0, $ry = 0)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function fill()
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function strokeRect($x, $y, $w, $h)
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function stroke()
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function endPath()
    {
        // TODO: Implement this function.
        echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function measureText($text)
    {
        // TODO: Implement this function.
        //echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    public function getStyle()
    {
        // TODO: Implement this function.
        // echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
        return $this->style;
    }

    public function setStyle(Style $style)
    {
        // TODO: Implement this function.
        // echo __FUNCTION__ . "\n";
        $this->style = $style;
    }

    private function getFont($family, $style)
    {
        // TODO: Implement this function.
        // echo __FUNCTION__ . " " . implode(',',func_get_args()) . "\n";
    }

    // Additional functions are below

    public function setPlotter(Plotter $object)
    {
        $this->plotter = $object;
    }

    private function log($args)
    {
        if ($this->debug) {
            var_dump($args);
        }
    }

    private function setRatioAndOffsets()
    {
        // Set ratio based on horizontal size
        $this->ratio = $this->plotter->getWidth() / $this->width;

        // Check if this will fit vertically
        if ($this->height * $this->ratio > $this->plotter->getHeight()) {
            // if it doesn't fit then use vertical ratio
            $this->ratio = $this->plotter->getHeight() / $this->height;
        }

        // Get offsets
        $this->offsetX = ($this->plotter->getWidth() - $this->width * $this->ratio) / 2;
        $this->offsetY = ($this->plotter->getHeight() - $this->height * $this->ratio) / 2;
    }

    private function translateToPage($x, $y)
    {
        if ($this->ratio === null) $this->setRatioAndOffsets();

        return array(
            $this->offsetX + $x * $this->ratio,
            $this->offsetY + $y * $this->ratio
        );
    }

}
