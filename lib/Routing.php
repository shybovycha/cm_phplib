<?php

	/**
	 *  @copyright Cloudmade, 2010
	 *  @license license.txt
	 */

	require_once "Cloudmade.php";

	/**
	 *  Class corresponding for CloudMade's routing service
	 *
	 *  @see RoutingService
	 *  @package Cloudmade
	 */

	class RoutingService extends Service
	{
		/*var $ROUTE_TYPES = array('car', 'foot', 'bicycle');
		var $OUTPUT_FORMATS = array('js', 'json', 'gpx');
		var $STATUS_OK = 0, $STATUS_ERROR = 1, $EARTHS_DIRECTIONS = array("N", "NE", "E", "SE", "S", "SW", "W", "NW");
		var $TURN_TYPE = array("C", "TL", "TSLL", "TSHL", "TR", "TSLR", "TSHR", "U");*/

		/**
		 *  Constructor. For internal use only.
		 */

		function RoutingService($_client, $_subdomain = null)
		{
			$subdomain = ($_subdomain == null) ? "routes" : $_subdomain;

			parent::Service($_client, $subdomain);
		}

		/**
		 *  Build route.
		 *
		 *  @param Point start_point Starting point
		 *  @param Point end_point Ending point
		 *  @param string route_type Type of route, e.g. 'car', 'foot', etc.
		 *  @param array transit_points List of {@link Point} objects route must visit before reaching end.
		 *   Points are visited in the same order they are specified in the sequence.
		 *  @param string route_type_modifier Modifier of the route type
		 *  @param string lang Language code in conformance to `ISO 3166-1 alpha-2` standard
		 *  @param string units Measure units for distance calculation
		 *
		 *  @return Route Returns {@link Route} object that was found.
		 *
		 *  @todo Make possible passing all these args as a hash.
		 */

		function route($_start_point, $_end_point, $_transit_points = null, $_route_type = "car", $_units = "km", $_lang = "en", $_route_type_modifier = null)
		{
			if ($_transit_points != null)
			{
				$s = ",[";

				foreach ($_transit_points as $i)
					$s .= $i->toLatLon() . ",";

				$s = trim($s, ", ");
				$s .= "]";

				$_transit_points = $s;
			}

			if ($_start_point == null || $_end_point == null)
				return null;

			if ($_route_type_modifier != null)
			{
				$_route_type_modifier = "/" . $_route_type_modifier;
			}

			$url = "/" . $_start_point->toLatLon() . $_transit_points . ",";
			$url .= $_end_point->toLatLon() . "/" . $_route_type . $_route_type_modifier;
			$url .= ".js?lang=" . $_lang . "&units=" . $_units;

			return new Route(json_decode(parent::connect($url), true));
		}

		/**
		 *  Internal utility method.
		 *
		 *  @return string This service's base URL.
		 */

		function urlTemplate()
		{
			return parent::urlTemplate() . "/api/0.3";
		}
	};

	/**
	 *  @see RouteSummary
	 *  @package Cloudmade
	 *
	 *  Statistics of the route.
	 *
	 *  @param float $total_distance Summary path' length.
	 *  @param float $total_time Summary path' time.
	 *  @param {@link Point} $start_point Path's start point.
	 *  @param {@link Point} $end_point Path's end point.
	 *  @param array $transit_points {@link Point} objects array, path goes through.
	 *
	 *  @todo toString() method
	 */

	class RouteSummary
	{
		var $total_distance, $total_time, $start_point, $end_point, $transit_points;

		/**
		 *  Constructor. For internal API use only.
		 */

		function RouteSummary($_summary)
		{
			if (!is_array($_summary) || is_null($_summary))
				return null;

			if (array_key_exists("total_distance", $_summary))
				$this->total_distance = floatval($_summary["total_distance"]);

			if (array_key_exists("total_time", $_summary))
				$this->total_time = floatval($_summary["total_time"]);

			if (array_key_exists("start_point", $_summary))
				$this->start_point = $_summary["start_point"];

			if (array_key_exists("end_point", $_summary))
				$this->end_point = $_summary["end_point"];

			if (array_key_exists("transit_points", $_summary))
				$this->transit_points = $_summary["transit_points"];
		}
	};

	/**
	 *  @see Route
	 *  @package Cloudmade
	 *
	 *  Wrapper around raw data being returned by routing service.
	 *
	 *  @param RouteInstruction $instructions Route instructions list.
	 *  @param RouteSummary $summary Route's statistic info.
	 *	@param Geometry $geometry Route's geometry.
	 *	@param string $version Routing HTTP API version.
	 *	@param int $status Response status.
	 *  @param int $status_message Response status.
	 */

	class Route
	{
		var $instructions, $summary, $geometry, $version, $status, $status_message;

		//var $STATUS_OK = 0, $STATUS_ERROR = 1;

		/**
		 *  Constructor. For internal use only.
		 */
		function Route($_data)
		{
			try
			{
				if (array_key_exists("status", $_data))
					$this->status = intval($_data["status"]);

				if (array_key_exists("route_instructions", $_data))
				{
					$this->instructions = array();

					foreach ($_data["route_instructions"] as $i)
						$this->instructions[] = new RouteInstruction($i);
				}

				if (array_key_exists("route_summary", $_data))
					$this->summary = new RouteSummary($_data["route_summary"]);

				if (array_key_exists("route_geometry", $_data))
					$this->geometry = new Line($_data["route_geometry"]);

				if (array_key_exists("version", $_data))
					$this->version = $_data["version"];

				if (array_key_exists("status_message", $_data))
					$this->status_message = $_data["status_message"];
			} catch (Exception $e)
			{
				throw new Exception("RouteNotFound");
			}
			
			if (empty($_data))
				throw new Exception("RouteNotFound");
		}

		/**
		 *  Converts {@link Route} to string.
		 *
		 *  @return string Route's string value.
		 */

		function toString()
		{
			$s = "";

			foreach ($this->instructions as $i)
				$s .= $i->toString() . "  ;  ";

			return $s;
		}
    };

	/**
	 *  @see RouteInstruction
	 *  @package Cloudmade
	 *
	 *  Instructions on route passing.
	 *
	 *  @param string $instruction Text instruction
	 *  @param float $length Length of the segment in meters
	 *  @param int $position Index of the first point of the segment
	 *  @param float $time Estimated time required to travel the segment in seconds
	 *  @param string $length_caption Length of the segments in specified units
	 *  @param string $earth_direction Earth direction
	 *  @param string $azimuth North-based azimuth
	 *  @param string $turn_type Code of the turn type
	 *  @param float $turn_angle Angle in degress of the turn between two segments
	 */

	class RouteInstruction
	{
		var $instruction, $length, $position, $time, $length_caption, $earth_direction;
		var $azimuth, $turn_type, $turn_angle;

		/**
		 *  Constructor. For internal use only.
		 */

		function RouteInstruction($_data)
		{
			if (is_null($_data) || !is_array($_data))
				return null;

			$this->instruction = $_data[0];
			$this->length = floatval($_data[1]);
			$this->position = intval($_data[2]);
			$this->time = intval($_data[3]);
			$this->length_caption = $_data[4];
			$this->earth_direction = $_data[5];
			$this->azimuth = floatval($_data[6]);

			if (count($_data) == 9)
			{
				$this->turn_type = $_data[7];
				$this->turn_angle = $_data[8];
			}
		}

		/**
		 *  "ToString" converter. Converts RouteInstruction object to string.
		 *
		 *  @return string RouteInstruction's string representation.
		 */

		function toString()
		{
		    return $this->instruction;
		}
	};

?>
