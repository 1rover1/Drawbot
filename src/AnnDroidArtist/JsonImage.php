<?php

namespace Rover2011\AnnDroidArtist;

class JsonImage
{
    private $drawing;

    private $width;
    private $height;

    private $ratio;

    private $plotter;

    public function __construct($filename)
    {
        $this->loadFile($filename);
    }

    protected function loadFile($filename)
    {
        $drawing = json_decode(file_get_contents($filename), true);

        //$drawing = $this->optimise($drawing);

        // Get max/min values
        $minX = 9999999;
        $minY = 9999999;
        $maxX = -9999999;
        $maxY = -9999999;

        // Need to make sure that the first object is a FeatureCollection
        if ($drawing['type'] !== 'FeatureCollection') {
            throw new \Exception("Not sure how to process GeoJson type of " . $drawing['type'], 1);
        }

        $this->drawing = [];

        // Go through each Feature
        foreach ($drawing['features'] as $feature){

            if ($feature['type'] !== 'Feature') {
                throw new \Exception("Not sure how to handle " . $feature['type'] . " feature type.", 1);
            }

            if ($feature['geometry']['type'] !== 'Polygon') {
                throw new \Exception("Not sure how to handle " . $feature['geometry']['type'] . " geometry type.", 1);
            }

            $polygon = [];

            foreach ($feature['geometry']['coordinates'][0] as $point) {
                // Check minimums and maximums
                if ($point[0] < $minX) $minX = $point[0];
                if ($point[1] < $minY) $minY = $point[1];
                if ($point[0] > $maxX) $maxX = $point[0];
                if ($point[1] > $maxY) $maxY = $point[1];

                // Add to polygon path
                $polygon[] = $point;
            }

            // Remove the last point from the polygon, if it's the same as the first
            if ($polygon[0] === $polygon[count($polygon) - 1]) {
                array_pop($polygon);
            }

            $this->drawing[] = $polygon;
        }

        // Set image dimensions.
        // Adding the min values gives a consistent border across vert/horiz sides.
        $this->width = $maxX + $minX;
        $this->height = $maxY + $minY;
    }

    protected function optimise($original)
    {
        return $original;
    }

    public function render(Plotter $plotter)
    {
        $this->plotter = $plotter;

        foreach ($this->drawing as $polygon) {

            // Move pen to start of polygon
            list ($startX, $startY) = $this->translateToPage($polygon[0][0], $polygon[0][1]);
            $this->plotter->moveTo($startX, $startY);

            // Draw polygon
            for ($pointIndex = 1; $pointIndex < count($polygon); $pointIndex++) {
                list ($drawX, $drawY) = $this->translateToPage($polygon[$pointIndex][0], $polygon[$pointIndex][1]);
                $this->plotter->drawTo($drawX, $drawY);
            }

            // Draw back to starting point
            list ($drawX, $drawY) = $this->translateToPage($polygon[0][0], $polygon[0][1]);
            $this->plotter->moveTo($drawX, $drawY);
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
