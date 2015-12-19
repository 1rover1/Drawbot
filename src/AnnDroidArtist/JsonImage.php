<?php

namespace Rover2011\AnnDroidArtist;

class JsonImage
{
    private $drawing;
    private $width;
    private $height;

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
                $polygon = array_pop($polygon);
            }

            $this->drawing[] = $polygon;
        }

        // Set image dimensions.
        // Adding the min values gives a consistent border across vert/horiz sides.
        $this->width = $maxX + $minX;
        $this->height = $maxY + $minY;

        /*
        echo "Min X = " . $minX . "\n";
        echo "Min Y = " . $minY . "\n";
        echo "Max X = " . $maxX . "\n";
        echo "Max Y = " . $maxY . "\n";
        */
    }

    protected function optimise($original)
    {
        return $original;
    }
}
