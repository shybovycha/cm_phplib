<?php

	/**
	 *  @copyright Cloudmade, 2010
	 *  @license license.txt
	 */

	require_once "Cloudmade.php";

	/**
	 *  Client Class
	 *  Represents client-side actions used for connecting to Cloudmade's
	 *  service servers and receiving query results.
	 *
	 *  @package Cloudmade
	 *  @see Client
	 *
	 *  @property object $connection Client-side {@link Connection} object
	 *  @property object $tile {@link TileService} object
	 *  @property object $geocoding {@link GeocodingService} object
	 *  @property object $routing {@link RoutingService} object
	 *  @property object $vectors {@link VectorStreamService} object
	 *  @property object $static_map {@link StaticMapsService} object
	 */

	class Client
	{
		var $connection, $tile, $geocoding, $routing, $vectors, $static_map;

		/**
		 *  Default constructor
		 *
		 *  Possible constructor calls:
		 *  <code>
		 *  <?php
		 *    $client = new Client($conn);
		 *  ?>
		 *  </code>
		 *
		 *  <code>
		 *  <?php
		 *    $client = new Client("APIKEY_GOES_HERE", "http://your.url.goes.here");
		 *  ?>
		 *  </code>
		 *
		 *  <code>
		 *  <?php
		 *    $client = new Client("APIKEY_GOES_HERE", "http://your.url.goes.here", 80);
		 *  ?>
		 *  </code>
		 *
		 *  @param string|object $_arg0 Could be either existing {@link Connection} object or your {@link http://support.cloudmade.com/answers/api-keys-and-authentication API key}
		 *  @param string|null $_base_url Is the url of the service's server you want get connect to. Could be string or null (<h3><b>Only if $_arg0 is an object!</b></h3>).
		 *  @param int|null $_port Is the port number you wish to use for connection. Could be either int or null
		 *
		 *  @return object Client object, used to get access to different services
		 */

		function Client($_arg0, $_base_url = null, $_port = null)
		{
			if (is_object($_arg0))
			{
				$this->connection = $_arg0;
			} else
			{
				$this->connection = new Connection($_arg0, $_base_url, $_port);
			}

			$this->tile = new TileService($this->connection);
			$this->geocoding = new GeocodingService($this->connection);
			$this->routing = new RoutingService($this->connection);
			$this->vectors = new VectorStreamService($this->connection);
			$this->static_map = new StaticMapsService($this->connection);
		}

		/**
		 *  Getter for the TileService object.
		 *
		 *  @return object|null Returns an object of {@link TileService}. Could be null if construction of a {@link Client} object failed.
		 */

		function getTileService()
		{
			return $this->tile;
		}

		/**
		 *  Getter for the GeocodingService object.
		 *
		 *  @return object|null Returns an object of {@link GeocodingService}. Could be null if construction of a {@link Client} object failed.
		 */

		function getGeocodingService()
		{
			return $this->geocoding;
		}

		/**
		 *  Getter for the GeocodingService object.
		 *
		 *  @return object|null Returns an object of {@link GeocodingService}. Could be null if construction of a {@link Client} object failed.
		 */

		function getRoutingService()
		{
			return $this->routing;
		}

		/**
		 *  Getter for the VectorStreamService object.
		 *
		 *  @return object|null Returns an object of {@link VectorStreamService}. Could be null if construction of a {@link Client} object failed.
		 */

		function getVectorStreamingService()
		{
			return $this->vectors;
		}

		/**
		 *  Getter for the StaticMapsService object.
		 *
		 *  @return object|null Returns an object of {@link StaticMapsService}. Could be null if construction of a {@link Client} object failed.
		 */

		function getStaticMapsService()
		{
			return $this->static_map;
		}

		/**
		 *  Get's tile using {@link TileService} object.
		 *
		 *  @param float $_lat Latitude coordinate of object's centroid. Mandatory.
		 *  @param float $_lon Longitude coordinate of object's centroid. Mandatory.
		 *  @param int|null $_zoom Scale of the tile. Could be 2<sup>x</sup> (x: [0, 8]) or null. Defaults to 1.
		 *  @param int|null $_style_id Style the tile will be drawn with. Could be [1, inf] or null. Defaults to 1.
		 *  @param int|null $_tile_size Size of a tile. Could be 2<sup>x</sup> (x: [5, 8]) or null. Defaults to 256.
		 *
		 *  @return file Image file contents (PNG image format).
		 */

		function getTile($_lat, $_lon, $_zoom = 1, $_style_id = null, $_tile_size = null)
		{
			return $this->tile->getTile($_lat, $_lon, $_zoom, $_style_id, $_tile_size);
		}

		/**
		 *  Get's objects, merged by query conditions.
		 *
		 *  @param array $_query Filter conditions. Associated array, where key is:
		 *  <ul>
		 *    <li>
		 *       <b> results </b> 
		 *       Number of results to return.  
		 *    </li>
		 *
		 *    <li>
		 *       <b> skip </b> 
		 *       Number of results to skip from beginning. 
		 *    </li>
		 *
		 *    <li>
		 *       <b> return_geometry  </b> 
		 *       Set it to <tt> true </tt> if you want geometry included in search results. 
		 *    </li>
		 *
		 *    <li>
		 *       <b> return_location </b> 
		 *       Set it to <tt> true </tt> if you do want location information like road, city, county, country, postcode in returned results. 
		 *    </li>
		 *
		 *    <li>
		 *       <b> bbox </b> 
		 *       Bounding box of the search area. Format: southern_latitude,western_longitude,northern_latitude,eastern_longitude. Cannot be used together with <b> around / distance </b>. 
		 *    </li>
		 *
		 *    <li>
		 *       <b> around </b> 
		 *       Center point of the search area. Used together with <b> distance </b>. <b> around </b> must be an EPSG:4326 coordinate ("latitude,longitude") or an address. Cannot be used together with <b> bbox </b>. If used together with a non-empty search query, only one may specify an address. 
		 *    </li>
		 *
		 *    <li>
		 *       <b> distance </b> 
		 *       Radius of the search area. Distance is specified in meters from the center point. Special value <b> closest </b> limits search results to one, closest to the center point of the search area. 
		 *    </li>
		 *
		 *    <li>
		 *       <b> bbox_only </b> 
		 *       Used only if <b> bbox </b> is specified. If set to false, the geocoder will return results from the whole planet, but still ranking results from within the specified bbox higher, otherwise only results from within the specified <b> bbox </b> will be returned. 
		 *    </li>
		 *
		 *    <li>
		 *       <b> object_type  </b> 
		 *       Limits search results to a specific object type. Full list of object types can be found {@link http://developers.cloudmade.com/wiki/geocoding-http-api/Object_Types here}. 
		 *    </li>
		 *  </ul>
		 *
		 *  @return object|null Returns {@link GeoResults} object or null if nothing was found.
		 */

		function find($_options)
		{
			return $this->geocoding->find($_options);
		}

		/**
		 *  Shows path from one point to another one through optional transit points.
		 *
		 *  @param Point $_start_point Begin of your path; your location at the moment.
		 *  @param Point $_end_point End of your path; your target; place you want get to.
		 *  @param array $_transit_points Array of {@link Point} objects, you want to get through. Defaults to null.
		 *  @param string $_route_type Type of future path. Could be "car", "foot" or "bicycle". Defaults to "car".
		 *  @param string $_lang UI language. Defaults to "en".
		 *  @param string $_route_type_modifier Distance units (?).
		 *
		 *  @return object|null Returns {@link Route} object or null if error has been occured.
		 */

		function route($_start_point, $_end_point, $_transit_points = null, $_route_type = "car", $_lang = "en", $_route_type_modifier = null)
		{
			return $this->routing->route($_start_point, $_end_point, $_transit_points, $_route_type, $_lang, $_route_type_modifier);
		}

		/**
		 *  Gets some region tiles.
		 *
		 *  @param BBox $_bbox Bounding box of region, you want to show.
		 *  @param string $_datatype Type of image will be generated. Could be "svg" or "svgz". Defaults to null.
		 *  @param array $_options Associated array of optional params. Defaults to null.
		 *
		 *  @return string Image file content.
		 */

		function getTileFromBBox($_bbox, $_datatype = null, $_options = null)
		{
			return $this->vectors->getTileFromBBox($_bbox, $_datatype, $_options);
		}

		/**
		 *  Gets concrete tile, based on it's longitude, latitude and zoom.
		 *
		 *  @param float @_lat Latitude of the tile.
		 *  @param float @_lon Longitude of the tile.
		 *  @param int @_zoom Zooming of the tile.
		 *  @param array $_options Possible optional params. Defaults to null.
		 *
		 *  @return string|null Output file content or null if something went wrong.
		 */

		function getTileFromCoords($_lat, $_lon, $_zoom, $_options = null)
		{
			return $this->vectors->getTileFromCoords($_lat, $_lon, $_zoom, $_options);
		}

		/**
		 *  Delivers rich map data from an up-to-date online database in a variety of common formats.
		 *
		 *  @param int $_size Size of the tile. Should be power of two
		 *  @param BBox|Point $_arg0 Could be the only argument (besides $_size) if it's {@link BBox} object or
		 *    can be one of the two - $_arg0 and $_arg1 if both of 'em are {@link Point} objects.
		 *  @param Point|null $_arg1 Used in pair with $_arg0 if it's a {@link Point} object only.
		 *  @param array $_options Associated array of possible options.
		 *
		 *  @return string|null Output file content or null if something went wrong.
		 */

		function getStaticMap($_size, $_arg0, $_arg1 = null, $_options = null)
		{
			return $this->static_map->getMap($_size, $_arg0, $_arg1, $_options);
		}
	};

?>
