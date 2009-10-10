<?php

/**
 * Routing service
 * 
 * @package Routing
 * @author Artem Shybovych
 * @copyright Cloudmade, 2009
 */

require_once 'Geometry.php';

if (!isset($_TEST_))
	require_once 'Connection.php';

/**
 * Cloudmade's Routing service class
 */

class Routing {
    var $ROUTE_TYPES = array('car', 'foot', 'bicycle');
    var $OUTPUT_FORMATS = array('js', 'json', 'gpx');
    var $STATUS_OK = 0;
    var $STATUS_ERROR = 1;
    var $EARTHS_DIRECTIONS = array("N", "NE", "E", "SE", "S", "SW", "W", "NW");
    var $TURN_TYPE = array("C", "TL", "TSLL", "TSHL", "TR", "TSLR", "TSHR", "U");

	var $connection, $sub_domain;

	/**
	 * Constructor. Creates Routing object based on connection param given.
	 * 
	 * @param Connection $_connection Cloudmade's service Connection object
	 */
	
    function Routing($_connection) {
		$this->sub_domain = 'routes';
		$this->connection = $_connection;
	}
	
	/**
	 * Function, that builds way from $start_point to $end_point through $_transit_points and returns Route object.
	 * 
	 * @param Point $start_point Point where to start building way from
	 * @param Point $end_point Point where the way finishes
	 * @param Point[] $_transit_points Array of points, the way must gi through
	 * @param string $route_type Type of the route. Could be either 'car', 'foot' or 'bicycle';
	 * @param string $lang Language code according to the `ISO 3166-1 alpha-2` standard
	 * @param string $route_type_modifier Measure units for the distance calculation
	 */
	
	function route($start_point, $end_point, $_transit_points = null, $route_type = 'car', $lang = 'en', $_route_type_modifier = null) {
		if (isset($transit_points))
			for ($i = 0; $i < count($_transit_points); $i++)
				$transit_points = $transit_points . ',[' . $_transit_points[$i]->to_string() . ']';
      
		if (isset($_route_type_modifier))
			$route_type_modifier = '/' . $_route_type_modifier;
		
		$uri = '/api/0.3/' . $start_point->to_string() . $transit_points . ',' . $end_point->to_string();
		$uri = $uri . '/' . $route_type . $route_type_modifier . '.js?lang=' . $lang . '&units=km';
      
		return $this->_call_service($uri);
    }
    
    /**
     * Make the call to CloudMade's service using underlying connection. This is an internal method and shouldn't be used directly. 
     * 
	 * @return Requested route
     * 
     * @param string $uri Request string
     */ 
    function _call_service($uri) {
		$raw = $this->connection->call_service($uri, $this->sub_domain);
        
        return new Route(json_decode($raw, true));
	}
}

/**
 * Statistics of the route
 */

class RouteSummary {
	var $total_distance, $total_time, $start_point, $end_point, $transit_points;

	/**
	 * Constructor
	 * 
	 * @param string $summary JSON representation of summary instructions
	 */
	
    function RouteSummary($summary) {
		$this->total_distance = floatval($summary['total_distance']);
        $this->total_time = floatval($summary['total_time']);
        $this->start_point = $summary['start_point'];
        $this->end_point = $summary['end_point'];
        $this->transit_points = $summary['transit_points'];
    }
}


/**
 * Wrapper around raw data being returned by routing service
 * 
 * @param array $instructions List of instructions
 * @param RouteSummary $summary Statistical info about the route
 * @param Line $geometry Geometry of route
 * @param string $version Version of routing HTTP API
 */

class Route {
	var $instructions, $summary, $geometry, $version, $status, $status_message;

    var $STATUS_OK = 0, $STATUS_ERROR = 1;
    
    /**
     * Constructor
     * 
     * @param array $data - JSON representation of the data
     */

    function Route($data) {
        $this->status = intval($data['status']);
        
        for ($i = 0; $i < count($data['route_instructions']); $i++)
			$this->instructions[] = new RouteInstruction($data['route_instructions'][$i]);
        
        $this->summary = new RouteSummary($data['route_summary']);
        $this->geometry = new Line($data['route_geometry']);
        $this->version = $data['version'];
        $this->status_message = $data['status_message'];
    }
	
	function instruction() {
		return $this->instructions;
	}
}

/**
 * Instructions on route passing
 * 
 * @param string $instruction
 * @param float $length
 * @param int $position
 * @param int $time
 * @param string $length_caption
 * @param string $earth_direction
 * @param float $azimuth
 * @param string $turn_type
 * @param float $turn_angle
 */

class RouteInstruction {
	var $instruction, $length, $position, $time, $length_caption, $earth_direction;
    var $azimuth, $turn_type, $turn_angle;

	/**
	 * Constructor
	 */
    function RouteInstruction($data) {
		$this->instruction = $data[0];
		$this->length = floatval($data[1]);
		$this->position = intval($data[2]);
		$this->time = intval($data[3]);
		$this->length_caption = $data[4];
		$this->earth_direction = $data[5];
		$this->azimuth = floatval($data[6]);
		
		if (count($data) == 9) {
			$this->turn_type = $data[7];
			$this->turn_angle = $data[8];
		}
	}
}

/**
 * Simplified access to Routing service.
 * 
 * @param Connection $_connection Connection object.
 * @param Point $start_point Point, which is a start of the way.
 * @param Point $end_point Point, which represents the end of the way.
 * @param Array(Point) $_transit_points Points, which way should pass through.
 * @param String $route_type Type of the way. Could be 'car', 'foot' or 'bicycle'
 * @param String $lang Language of instructions, which will be generated.
 * @param String $_route_type_modifier Route type modifier if needed.
 * 
 * @return Array of RouteInstruction objects.
 */

function cm_route($connection, $start_point, $end_point, $_transit_points = null, $route_type = 'car', $lang = 'en', $_route_type_modifier = null) {
	$routing = new Routing($connection);
	
	return $routing->route($start_point, $end_point, $_transit_points, $route_type, $lang, $_route_type_modifier)->instruction();
}
?>
