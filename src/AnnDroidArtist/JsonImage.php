<?php

namespace Rover2011\AnnDroidArtist;

class JsonImage
{
    private $drawing;

    private $width;
    private $height;

    private $ratio;

    private $plotter;

    private $pointCount;
    private $lineCount;

    public function __construct($filename, $plotter)
    {
        $this->lineCount = 0;
        $this->pointCount = 0;

        $this->loadFile($filename);
        $this->plotter = $plotter;
    }

    protected function loadFile($filename)
    {
        $drawing = json_decode(file_get_contents($filename), true);

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

            $this->lineCount++;

            foreach ($feature['geometry']['coordinates'][0] as $point) {
                // Check minimums and maximums
                if ($point[0] < $minX) $minX = $point[0];
                if ($point[1] < $minY) $minY = $point[1];
                if ($point[0] > $maxX) $maxX = $point[0];
                if ($point[1] > $maxY) $maxY = $point[1];

                // Add to polygon path
                $polygon[] = $point;

                $this->pointCount++;
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

    public function getPointCount()
    {
        return $this->pointCount;
    }

    public function getLineCount()
    {
        return $this->lineCount;
    }

    public function optimise()
    {
        // This optimise function will start off by finding the closest point
        // to the pen and draw to it. It will continue to do this for each
        // polygon in the feature collection

        // Get pen position - in coordinates used by GeoJson

        $pen[0] = $this->plotter->getX();                                                           // Is in plotter coords
        $pen[0] = $pen[0] + $this->plotter->getMotorDistance() / 2 - $this->plotter->getPageLeft(); // Now in page coords
        $pen[0] = $pen[0] / $this->plotter->getWidth() * $this->width;                              // Now in GeoJson coords

        $pen[1] = abs($this->plotter->getY());                                                      // Is in plotter coords
        $pen[1] = $this->plotter->getPageTop() + $this->plotter->getHeight() - $pen[1];             // Now in page coords
        $pen[1] = $pen[1] / $this->plotter->getHeight() * $this->height;                            // Now in GeoJson coords


        $polygons = $this->drawing;
        $optimisedDrawing = [];

        $usedPolygons = 0;

        while ($usedPolygons < count($polygons)) {

            $minimumDistance = 99999;
            $nextPolygon = -1;
            $nextPolygonPoint = -1;

            // Find the next closest point
            for ($polygonIndex = 0; $polygonIndex < count($polygons); $polygonIndex++) {
                if (!isset($polygons[$polygonIndex]['used'])) {
                    for ($pointIndex = 0; $pointIndex < count($polygons[$polygonIndex]); $pointIndex++) {

                        $distance = $this->distanceBetweenPoints($polygons[$polygonIndex][$pointIndex], $pen);
                        if ($distance < $minimumDistance) {
                            $minimumDistance = $distance;
                            $nextPolygon = $polygonIndex;
                            $nextPolygonPoint = $pointIndex;
                        }

                    }
                }
            }

            // Copy that polygon into the optimised drawing, starting from the closest point
            $optimisedDrawing[] = array_merge(
                array_slice($polygons[$nextPolygon], $nextPolygonPoint),
                array_slice($polygons[$nextPolygon], 0, $nextPolygonPoint)
            );

            // Update pen position
            $pen = $optimisedDrawing[count($optimisedDrawing) - 1][0];

            // Update statistics and counters and shit
            $polygons[$nextPolygon]['used'] = true;
            $usedPolygons++;

            if ($usedPolygons % 100 === 0) {
                echo "Date: " . date("r") . " : " . round(100 * $usedPolygons / $this->getLineCount(), 2) . "\n";
            }
        }

        $this->drawing = $optimisedDrawing;
    }

    public function render()
    {
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

    private function distanceBetweenPoints($point1, $point2)
    {
        $x1 = $point1[0];
        $x2 = $point2[0];
        $y1 = $point1[1];
        $y2 = $point2[1];

        return sqrt(($y2 - $y1) * ($y2 - $y1) + ($x2 - $x1) * ($x2 - $x1));
    }

}
