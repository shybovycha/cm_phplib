<?php

/**
 * @package Routing
 * @author Artem Shybovych
 */

if (!isset($_TEST_))
	require "Connection.php";

/**
 * Class that is responsible for tile services of Cloudmade
 *
 * @param string $subdomain Subdomain of CloudMade's tile service
 * @param Connection $connection Connection object to be used by CloudMade's tile service
 * @param int $tile_size Length of requested tiles' sides
 * @param int $style_id Style id of the requested tile
 */

class Tile {
	var $subdomain, $connection, $tile_size, $style_id;

	/**
	 * Constructor
	 * 
	 * @param Connection $connection Connection object to be used by CloudMade's tile service
	 * @param int $tile_size Length of requested tiles' sides
	 * @param int $style_id Style id of the requested tile
	 */
	
    function Tile($_connection, $_tile_size = 256, $_style_id = 1) {
		$this->subdomain = "tile";
		$this->connection = $_connection;
		$this->tile_size = $_tile_size;
		$this->style_id = $_style_id;
	}
	
	/**
	 * Get tile with given latitude, longitude and zoom
	 * 
	 * @param float $latitude Latitude of requested tile
	 * @param float $longitude Longitude of requested tile
	 * @param float $zoom Zoom level, on which tile is being requested
	 * 
	 * @return Raw PNG data that was returned by request
	 */
	function get($latitude, $longitude, $zoom) {
		$xy_tile = latlon2tilenums($latitude, $longitude, $zoom);
		
		$u = '/' . strval($this->style_id) . '/' . strval($this->tile_size) . '/';
	        $u = $u . strval($zoom) . '/' . strval($xy_tile[0]) . '/';
        	$u = $u . strval($xy_tile[1]) . '.png';

        	return $this->connection->call_service($u, $this->subdomain);
	}
}

/**
 * Convert latitude, longitude pair to tile coordinates
 * 
 * @param float $latitude Latitude
 * @param float $longitude Longitude
 * 
 * @return Tile coordinates
 */

function latlon2tilenums($lat, $lon, $zoom) {
	$factor = pow(2, ($zoom - 1));
	$lat = deg2rad($lat);
	$lon = deg2rad($lon);
	$xtile = 1 + $lon / M_PI;
	$ytile = 1 - log(tan($lat) + (1 / cos($lat))) / M_PI;
		
	return array(intval($xtile * $factor), intval($ytile * $factor));
}

/**
 * Convert tile coordinates pair to latitude, longitude
 * 
 * @param $xtile X coordinate of the tile
 * @param $ytile Y coordinate of the tile
 * @param float $zoom Zoom level
 * 
 * @return Latitude, longitude pair
 */

function tilenums2latlon($xtile, $ytile, $zoom) {
	$factor = pow(2.0, $zoom);
	$lon = ($xtile * 360 / $factor) - 180.0;
	$lat = atan(sinh(M_PI * (1 - 2 * $ytile / $factor)));
	
	return array(rad2deg($lat), $lon);
}

/**
 * A wrapper for getting tiles.

 *  This is a thin wrapper aroung tile.Tile's get() method. If you don't
 *  want to pass a connection object, you can specify
 *  connection.Connection arguments after all other arguments. For
 *  example:
 *  get_tile(47.26117, 9.59882, 10, 1, 256, new Connection('BC9A493B41014CAABB98F0471D759707'));
 */

function cm_get_tile($connection, $latitude, $longitude, $zoom, $style_id = 1,
		$size = 256) {
	
	$tile = new Tile($connection, $size, $style_id);

    return $tile->get($latitude, $longitude, $zoom);
}
?>
