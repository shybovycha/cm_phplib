<?php
/**
 * Different geometry types like Line, BoundingBox, Polygon, etc.
 * 
 * @package Geometry
 * @author Artem Shybovych
 */

/**
 * Base class for geometry objects
 * 
 * @return Point, Line, Multiline, Polygon, Multipolygon or null
 *
 */

function Geometry($data, $lat_lon = true) {
	switch (strtolower($data['type'])) {
		case 'point':
				return new Point($data['coordinates']);
			break;
					
		case 'linestring':
				return new Line($data['coordinates']);
			break;
				
		case 'multilinestring':
				return new MultiLine($data['coordinates']);;
			break;
					
		case 'polygon':
				return new Polygon($data['coordinates']);
			break;
					
		case 'mutlipolygon':
				return new MultiPolygon($data['coordinates']);
			break;
					
		default:
			return null;
	}
}

/**
 * Simple container of latitude, longitude pair
 *
 * @param float $lat Latitude
 * @param float $lon Longitude
 *
 */

class Point {
    public $latitude, $longitude;
    
    function Point($coordinates, $lat_lon = true) {
		if (count($coordinates) == 1)
            $coordinates = $coordinates[0];

        if (!$lat_lon)
            $coordinates = array_reverse($coordinates);

        $this->latitude = $coordinates[0];
        $this->longitude = $coordinates[1];
    }

	/**
	 * Text representation of point.
	 *
         * @param bool $lat_lon Declare order: True - lat lon, False - lon lat 
	 */
	
    function to_string($lat_lon = true) {
		return strval($this->latitude) . ',' . strval($this->longitude);
    }
}

/**
 * Geometry object that consists of Point geometries
 * 
 * @param array(Point) $points List of points, that make up the line
 */

class Line {
	public $points;
	
    function Line($_coordinates, $lat_lon = true) {
		for ($i = 0; $i < count($_coordinates); $i++)
			$this->points[] = new Point($_coordinates[$i], $lat_lon);
    }
}

/**
 * Geometry object that consists of Line geometries
 *
 * @param array(Line) $lines List of lines, that make up the multiline
 */

class MultiLine {
	public $points;
	
    function MultiLine($_coordinates, $lat_lon = true) {
		for ($i = 0; $i < count($_coordinates); $i++)
			$this->points[] = new Line($_coordinates[$i], $lat_lon);
    }
}

/**
 * Geometry object that consists of Line geometries and has a closed form
 *
 * @param array(Line) $holes List of lines that make up the holes in polygon
 * @param array(Line) $border_line Border line of polygon
 */

class Polygon {
	public $border_line, $holes;
	
    function Polygon($coordinates, $lat_lon = true) {
        $this->border_line = new Line($coordinates[0], $lat_lon);
        
        for ($i = 1; $i < count($coordinates); $i++)
			$this->holes = new Line($coordinates[$i], $lat_lon);
    }
}

/**
 * Geometry object that consists of Polygon geometries
 *
 * @param array(Polygon) $polygons List of polygons, that make up the multipolygon
 */

class MultiPolygon {
	public $polygons;
	
    function MultiPolygon($coordinates, $lat_lon = true) {
		for ($i = 0; $i < count($coordinates); $i++)
			$this->polygons[] = new Polygon($coordinates[$i], $lat_lon);
    }
}

/**
 * Bounding box object, which contains pair of points

 * @param array(Point) $points Pair of points, which correspond to low-left and
 * upper-right coordinates respectively
 */

class BoundingBox {
	public $points;
	
    function BoundingBox($coordinates, $lat_lon = true) {
		for ($i = 0; $i < count($coordinates); $i++) {
			$this->points = new Point($coordinates[$i]);
		}
    }

	/**
	 * Alternative constructor for BBox
	 *
         * Constructs object from two given points.
	 *
         * @param Point $point1 Low-left coordinate
         * @param Point $point2 Up-right coordinate
         * @return BBox instance
	 */
	
    function from_points($point1, $point2) {
        return new BoundingBox(array($point1, $point2));
    }

	/**
	 * Copy of default constructor
	 *
         * Constructs object from given sequence of points.
         *   
         * @param array(Point) $coordinates Pair of points, which correspond to low-left and
         * upper-right coordinates respectively
         * @return BBox instance
	 */
	
    function from_coordinates($coordinates, $lat_lon = true) {
        return new BoundingBox($coordinates, $lat_lon);
    }

	/**
	 * Text representation of bbox.
	 *
     * @param bool $lat_lon Declare order: True - lat lon, False - lon lat 
	 * @return String representation of BBox
	 */
	
    function to_string($lat_lon = true) {
        for ($i = 0; $i < count($this->points); $i++)
			$x[] = $this->points[$i]->to_string($lat_lon);
			
		return $x;
    }
}
?>
