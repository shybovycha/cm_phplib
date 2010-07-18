<?php

	/**
	 *  @copyright Cloudmade, 2010
	 *  @license license.txt
	 */

	/**
     *  Utility function: converts array of objects into string, e.g.:
	 *  Polygon::array(Point0, Point1, Point2)   =>   array("Point(x, y)", "Point(x, y)", "Point(x, y)")
	 *
	 *  @param object $arg Object you want to convert.
	 *  @return array Object in the array form.
	 */

	function arrayToString($arg)
	{
		if (is_array($arg) && is_object($arg[0]))
		{
			$s = "";

			foreach ($arg as $i)
				$s .= $i->toString() . ",";

			$s = trim($s, ", ");

			return $s;
		}

		return null;
	}

	/**
	 *  @see Geometry
	 *  @package Cloudmade
	 *
	 *  Base class for all geometries. You can use it if you don't know argument's type for sure.
	 *  For ex.: if you have some data in the array form and you know that it's a geometry, but don't
	 *  know which of them for sure, you can pass this data to the Geometry::parse method and the result
	 *  will be a concrete geometry object.
	 */

	class Geometry
	{
		/**
		 *  Parses data and converts it to the geometry object it must looks like.
		 *
		 *  @param array $_data Hash, giving all needed params.
		 *  @return Point|BBox|Line|MultiLine|Polygon|MultiPolygon Concrete geometry object.
		 */

		function parse($_data)
		{
			if ($_data == null)
				return null;

			switch (strtolower($_data["type"]))
			{
				case "point":
					return new Point($_data["coordinates"]);
				break;

				case "line":
					return new Line($_data["coordinates"]);
				break;

				case "multilinestring":
					return new MultiLine($_data["coordinates"]);
				break;

				case "polygon":
					return new Polygon($_data["coordinates"]);
				break;

				case "multipolygon":
					return new MultiPolygon($_data["coordinates"]);
				break;

				case "bbox":
					return new BBox($_data["coordinates"]);
				break;

				default:
					return null;
			}
		}
	};

	/**
	 *  @see Point
	 *  @package Cloudmade
	 *
	 *  Point class.
	 *
	 *  @property float $lat Latitude coordinate
	 *  @property float $lon Longitude coordinate
	 */

	class Point extends Geometry
	{
		var $lat, $lon;

		/**
		 *  Constructor.
		 *
		 *  Posible forms are:
		 *  <ul>
		 *    <li>new Point(1, -1)</li>
		 *    <li>new Point([1, -1])</li>
		 *  </ul>
		 *
		 *  @param array|float $_arg0 Could be the only argument if it's of an array type or the first item of coordinate values pair.
		 *  @param float|null $_arg1 Could be the second item of coordinate pair (if the first one is float) or null (if the first argument is an array).
		 */

		function Point($_arg0, $_arg1 = null)
		{
			if (is_array($_arg0) && count($_arg0) == 2 && is_null($_arg1))
			{
				$this->lat = $_arg0[0];
				$this->lon = $_arg0[1];
			} else
			if (!is_array($_arg0) && !is_array($_arg1))
			{
				$this->lat = $_arg0;
				$this->lon = $_arg1;
			}
		}

		/**
		 *  Boolean operator "==". Compares this point and the argument.
		 *
		 *  @param Point $v The second operand (the first one is "this").
		 *  @return true|false Returns true if $this == $v
		 */

		function isEqual($v)
		{
			return ($this->lat == $v->lat && $this->lon == $v->lon);
		}

		/**
		 *  String converter.
		 *
		 *  @return string This point's string form.
		 */

		function toString()
		{
			return "Point(" . strval($this->lat) . ", " . strval($this->lon) . ")";
		}

		/**
		 *  URL converter.
		 *
		 *  @return string This point's string form, acceptable by browser and {@link Connection} object.
		 *  @see Point_toUrl
		 */

		function toUrl()
		{
			return strval($this->lat) . "," . strval($this->lon);
		}

		/**
		 *  Lat-Lon converter. Converts $lat and $lon to a string. {@link Point_toUrl} alias.
		 *
		 *  @return string "%latitude%,%longitude%" string value, acceptable by browser and {@link Connection} object.
		 */

		function toLatLon()
		{
			return strval($this->lat) . "," . strval($this->lon);
		}
	};

	/**
	 *  @see Line
	 *  @package Cloudmade
	 *
	 *  Line class.
	 *
	 *  @property array $points {@link Point} objects array, forming line.
	 */

	class Line extends Geometry
	{
		var $points;

		/**
		 *  Line object constructor.
		 *
		 *  @param array $_coords Coordinates array. Should contain either {@link Point} or float items.
		 */

		function Line($_coords)
		{
			if (!is_array($_coords) || is_null($_coords))
				return null;

			if (!is_object($_coords[0]))
			{
				for ($i = 0; $i < count($_coords); $i += 2)
					$this->points[] = new Point($_coords[$i], $_coords[$i + 1]);
			} else
			{
				foreach ($_coords as $i)
					$this->points[] = $i;
			}
		}

		/**
		 *  String converter.
		 *
		 *  @return string This line's string representation.
		 */

		function toString()
		{
			return "Line(" . arrayToString($this->points) . ")";
		}

		/**
		 *  URL converter.
		 *
		 *  @return string URL string, acceptable by browser and {@link Connection} opbject.
		 */

		function toUrl()
		{
			$s = "";

			foreach ($this->points as $i)
				$s .= "[" . $i->toUrl() . "],";

			$s = trim($s, ", ");

			return $s;
		}
	};

	/**
	 *  @see MultiLine
	 *  @package Cloudmade
	 *
	 *  MultiLine class.
	 *
	 *  @property array $lines {@link Line} objects array.
	 */

	class MultiLine extends Geometry
	{
		var $lines;

		/**
		 *  Constructor.
		 *
		 *  @param array $_coords {@link Line} objects array.
		 */

		function MultiLine($_coords)
		{
			foreach ($_coords as $i)
				$this->lines[] = new Line($i);
		}

		/**
		 *  String converter.
		 *
		 *  @return string This object's string representation.
		 */

		function toString()
		{
			return "MultiLine(" . arrayToString($this->lines) . ")";
		}
	};

	/**
	 *  @see Polygon
	 *  @package Cloudmade
	 *
	 *  Polygon class.
	 *
	 *  @property Line $border_line Border line.
	 *  @property array $holes {@link Line} objects array. Parts of map, which don't belong to this polygon.
	 */

	class Polygon extends Geometry
	{
		var $border_line, $holes;

		/**
		 *  Constructor
		 *
		 *  @param array $_coords {@link Point} objects 2-dimensional array. First array item represents border line coordinates, while
		 *  other items set holes in the polygon.
		 *
		 *  @see Polygon_constructor
		 */

		function Polygon($_coords)
		{
			$this->border_line = new Line($_coords[0]);

			for ($i = 1; $i < count($_coords); $i++)
			{
				/*foreach ($_coords[$i] as $t)
					$this->holes[] = new Line($t);*/
					
				$this->holes[] = new Line($_coords[$i]);
			}
		}

		/**
		 *  String converter.
		 *
		 *  @return string String representation of this polygon.
		 */

		function toString()
		{
			return "Polygon(" . $this->border_line->toString() . " - (" . arrayToString($this->holes) . ")";
		}
	};

	/**
	 *  @see MultiPolygon
	 *  @package Cloudmade
	 *
	 *  MultiPolygon class.
	 *  @property array $polygons {@link Polygon} objects array.
	 */

	class MultiPolygon extends Geometry
	{
		var $polygons;

		/**
		 *  Constructor.
		 *
		 *  @param array $_coords Array, used to create all polygons, this MultiPolygon contains. Though, <b>$_coords</b> is
		 *  an array of two-dimansional arrays. See {@link Polygon_constructor} for detailed info.
		 */

		function MultiPolygon($_coords)
		{
			foreach ($_coords as $i)
				$this->polygons[] = new Polygon($i);
		}

		/**
		 *  String converter.
		 *
		 *  @return string Returns string representation of current object.
		 */

		function toString()
		{
			return "MultiPolygon(" . arrayToString($this->polygons) . ")";
		}
	};

	/**
	 *  @see BBox
	 *  @package Cloudmade
	 *
	 *  Bounding box class.
	 *
	 *  @property array $points {@link Point} objects array, setting this object.
	 */

	class BBox extends Geometry
	{
		var $points;

		/**
		 *  Constructor.
		 *
		 *  @param array $_points {@link Point} objects array or coordinates array (coordinate pairs array).
		 */

		function BBox($_points)
		{
			if (!is_array($_points))
				return null;

			if (is_object($_points[0]))
			{
				$this->points = $_points;
			} else
			if (is_array($_points[0]))
			{
				$this->points = array(new Point($_points[0][0], $_points[0][1]), new Point($_points[1][0], $_points[1][1]));
			}
		}

		/**
		 *  String converter.
		 *
		 *  @return string Returns string representation of current object.
		 */

		function toString()
		{
			return "BBox(" . arrayToString($this->points) . ")";
		}

		/**
		 *  URL converter.
		 *
		 *  @return string This object's string form, acceptable by browser and {@link Connection} object.
		 */

		function toUrl()
		{
			if (!is_array($this->points) || is_null($this->points))
				return null; else
					return $this->points[0]->toUrl() . "," . $this->points[1]->toUrl();
		}

		/**
		 *  Boolean operator "==". Compares this BBox and the argument.
		 *
		 *  @param BBox $_bbox The second operand (the first one is "this").
		 *  @return bool Returns true if $this == $_bbox
		 */

		function isEqual($_bbox)
		{
			return (($_bbox->points[0] == $this->points[0] && $_bbox->points[1] == $this->points[1]) ||
				    ($_bbox->points[0] == $this->points[1] && $_bbox->points[1] = $this->points[0]));
		}
	};

?>
