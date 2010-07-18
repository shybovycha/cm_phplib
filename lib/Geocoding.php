<?php

	/**
	 *  @copyright Cloudmade, 2010
	 *  @license license.txt
	 */

	require_once "Cloudmade.php";

	/**
	 *  Represents an interface to Cloudmade's geocoding services.
	 *
	 *  @see GeocodingService
	 *  @package Cloudmade
	 */

	class GeocodingService extends Service
	{
		/**
		 *  Constructor. As usual, uses just one argument - connection.
		 *
		 *  @param Connection $_client Connection object
		 *  @param string|null $_subdomain Optional. Sets custom sub-domain value. Null by default.
		 */

		function GeocodingService($_client, $_subdomain = null)
		{
			$subdomain = ($_subdomain == null) ? "geocoding" : $_subdomain;

			parent::Service($_client, $subdomain);
		}

		/**
		 *  Find objects that match given query. Returns GeoResults object.
		 *
		 *  @see Geocoding_find
		 *  @param array $_options Hash, giving search options, e.g. 'bbox', 'object_type', etc.
		 *  Possible keys are:
		 *  <ul>
		 *     <li>
		 *       <b>results</b> Number of results to return.
		 *    </li>
		 *    <li>
		 *       <b>skip</b> Number of results to skip from beginning.
		 *    </li>
		 *    <li>
		 *       <b>around</b> Center point of the search area. Used together with <b>distance</b>. <b>around</b> must be an EPSG:4326 coordinate ("latitude,longitude") or an address. Cannot be used together with <b>bbox</b>. If used together with a non-empty search query, only one may specify an address.
		 *    </li>
		 *    <li>
		 *       <b>distance</b>
		 *        Radius of the search area. Distance is specified in meters from the center point. Special value <b>closest</b> limits search results to one, closest to the center point of the search area.
		 *    </li>
		 *    <li>
		 *       <b>bbox</b>
		 *       Bounding box of the search area. Format: <i>southern_latitude,western_longitude,northern_latitude,eastern_longitude</i>. Cannot be used together with <b>around</b> / <b>distance</b>.
		 *    </li>
		 *    <li>
		 *       <b>bbox_only</b>
		 *       Used only if <b>bbox</b> is specified. If set to false, the geocoder will return results from the whole planet, but still ranking results from within the specified bbox higher, otherwise only results from within the specified <b>bbox</b> will be returned.
		 *    </li>
		 *    <li>
		 *       <b>return_geometry</b>
		 *       Set it to true if you want geometry included in search results.
		 *    </li>
		 *    <li>
		 *       <b>return_location</b>
		 *       Set it to true if you do want location information like road, city, county, country, postcode in returned results.
		 *    </li>
		 *    <li>
		 *       <b>object_type</b>
		 *       Limits search results to a specific object type. Full list of object types can be found {@link http://developers.cloudmade.com/wiki/geocoding-http-api/Object_Types here}.
		 *    </li>
		 *    <li>
		 *       <b>query</b>
		 *       Search query.
		 *    </li>
		 *    <li>
		 *       <b>visualize</b>
		 *       Sets if dynamic map (whith UI) will be drawn or not.
		 *    </li>
		 *  </ul>
		 *
		 *  @return GeoResults {@link GeoResults} object
		 */

		function find($_options)
		{
			$visualize = false;

			if ($_options == null || !is_array($_options))
				return null;

			if (array_key_exists("query", $_options))
				$_options["query"] = str_replace(" ", "+", $_options["query"]);

			if (array_key_exists("visualize", $_options))
			{
				if ($_options["visualize"])
					$visualize = true;

				unset($_options["visualize"]);
			}

			if (!array_key_exists("query", $_options))
			{
				if (!array_key_exists("results", $_options))
					$_options["results"] = 10;

				if (!array_key_exists("skip", $_options))
					$_options["skip"] = 0;

				if (!array_key_exists("bbox", $_options) && !array_key_exists("along", $_options) && !array_key_exists("around", $_options))
					$_options["around"] = new Point(0, 0);

				if (array_key_exists("around", $_options) && !array_key_exists("distance", $_options))
					$_options["distance"] = 16000;
			} else
			{
				unset($_options["distance"]);
				unset($_options["around"]);
				unset($_options["skip"]);
				unset($_options["results"]);
			}

			$query = "";

			foreach ($_options as $k => $v)
			{
				if (is_object($v))
					$query .= trim($k) . "=" . trim($v->toUrl()) . "&"; else
				if (isset($v) && !is_null($v))
					$query .= trim($k) . "=" . trim(strval($v)) . "&";
			}

			if (!$visualize)
			{
				$request = "/v2/find.js?" . trim($query, ",& ");
				return new GeoResults(json_decode(parent::connect($request), true));
			} else
			{
				$request = "/v2/find.html?" . trim($query, ",& ");
				return parent::connect($request);
			}
		}

		/**
		 *  Internal utility method.
		 *
		 *  @return string Service's URL.
		 */

		function urlTemplate()
		{
			return parent::urlTemplate() . "/geocoding";
		}
	};

	/**
	 *  Short info about GeoResult.
	 * 
	 *  @see GeoResultInfo
	 *  @package Cloudmade
	 *
	 *  @property string $type Result's type.
	 *  @property string $code Result's code.
	 *  @property int $coord_order Result's coordinates order. Could be 1 or 0.
	 */

	class GeoResultInfo
	{
		var $type, $code, $coord_order;

		/**
		 *  Constructor. For internal use only.
		 */
		function GeoResultInfo($_data)
		{
			if (!is_array($_data) || $_data == null)
				return null;

			if (array_key_exists("type", $_data))
				$type = $_data["type"];

			if (is_array($_data["properties"]))
			{
				if (array_key_exists("code", $_data["properties"]))
					$type = $_data["properties"]["code"];

				if (array_key_exists("coordinate_order", $_data["properties"]))
					$coord_order = $_data["properties"]["coordinate_order"];
			}
		}

		/**
		 *  String representation of this object.
		 *
		 *  @return string Returns short self description.
		 */
		function toString()
		{
			$s = "";

			if ($this->type != null)
				$s .= "Type: " . $this->type . "; ";

			if ($this->code != null)
				$s .= "Code: " . $this->code . "; ";

			if (is_array($this->coord_order))
				$s .= "CoordinateOrder: " . $this->coord_order[0] . "; "; else
					$s .= "CoordinateOrder: 0; ";

			return "GeoInfo [" . $s . "]";
		}
	};

	/**
	 *  Implements results, produced by {@link Geocoding_find} method for easy handling.
	 *
	 *  @see GeoResults
	 *  @package Cloudmade
	 * 
	 *  @property array $results {@link GeoResult} array.
	 *  @property int $found Found results count.
	 *  @property BBox $bounds Results BBox.
	 *  @property GeoResultInfo $crs {@link GeoResultInfo} object.
	 */

	class GeoResults
	{
		var $bounds, $found, $results, $crs;

		/**
		 *  Constructor.
		 *
		 *  @param array $_data Hash, giving all options needed for initialization. GeoResults objects are created by GeoCodingService::find only. You don't need to know the details of construction.
		 */
		function GeoResults($_data)
		{
			if (!is_array($_data) || $_data == null)
				return null;

			if (array_key_exists("bounds", $_data))
			{
				$this->bounds = new BBox($_data["bounds"]);
			}

			if (array_key_exists("crs", $_data))
				$this->crs = new GeoResultInfo($_data["crs"]);

			if (array_key_exists("found", $_data))
				$this->found = intval($_data["found"]);

			if (array_key_exists("features", $_data) && is_array($_data["features"]))
			{
				$this->results = array();

				foreach ($_data["features"] as $i)
					$this->results[] = new GeoResult($i);
			}
		}

		/**
		 *  String results representation.
		 *
		 *  @return string Returns some details 'bout self.
		 */
		function toString()
		{
			$s = "";

			if ($this->bounds != null)
				$s .= "Bounds: " . $this->bounds->toString() . "; ";

			if ($this->found != null)
				$s .= "Found: " . strval($this->found) . "; ";

			return "GeoResults [" . $s . "]";
		}
	};

	/**
	 *  GeoResults::results item. This one you will handle to produce user-friendly results.
	 * 
	 *  @see GeoResult
	 *  @package Cloudmade
	 *
	 *  @property Point $centroid Object's centroid.
	 *  @property BBox $bounds Results set' bounds.
	 *  @property int $id Object's ID.
	 *  @property Location $location Object's location (if set).
	 *  @property Geometry $geometry Object's geometry (if set).
	 *  @property array $properties Object's properties.
	 */

	class GeoResult
	{
		var $centroid, $bounds, $id, $location, $geometry, $properties;

		/**
		 *  Constructor. Used by {@link GeoResults} only.
		 */

		function GeoResult($_data)
		{
			if (!is_array($_data) || $_data == null)
				return null;

			if (array_key_exists("centroid", $_data))
				$this->centroid = Geometry::parse($_data["centroid"]);

			if (array_key_exists("bounds", $_data))
				$this->bounds = new BBox($_data["bounds"]);

			if (array_key_exists("id", $_data))
				$this->id = intval($_data["id"]);

			if (array_key_exists("properties", $_data))
				$this->properties = $_data["properties"];

			if (array_key_exists("location", $_data))
				$this->location = new Location($_data["location"]);

			if (array_key_exists("geometry", $_data))
				$this->geometry = new Geometry($_data["geometry"]);
		}

		/**
		 *  Short string self representation.
		 *
		 *  @return string Returns user-understandable properties string description.
		 */

		function toString()
		{
			$s = "";

			if ($this->id != null)
				$s .= "ID: " . strval($this->id) . "; ";

			if ($this->centroid != null)
				$s .= "Centroid: " . $this->centroid->toString() . "; ";

			if ($this->bounds != null)
				$s .= "Bounds: " . $this->bounds->toString() . "; ";

			if ($this->location != null && $this->location->name != null)
				$s .= "Location: " . $this->location->toString();

			if ($this->geometry != null)
				$s .= "Geometry: " . $this->geometry->toString();

			return "GeoResult ["  . $s . "]";
		}
	};

	/**
	 *  Implements back-end interface for each GeoResult::location object.
	 * 
	 *  @see Location
	 *  @package Cloudmade
	 *
	 *  @property string $country Country, where the object is situated.
	 *  @property string $county County, where the object is situated.
	 *  @property string $city City, where the object is situated.
	 *  @property string $postcode Object's post code (if set).
	 *  @property string $street Street, where the object is situated.
	 *  @property string $housenumber Object's house number (if set).
	 *  @property string $name Object's name.
	 *  @property string $osm_id Object's OSM ID.
	 *  @property string $amenity Object's amenity.
	 *  @property string $cuisine Object's cuisine.
	 */

	class Location
	{
		var $country, $city, $postcode, $street, $housenumber;
		var	$name, $osm_id, $amenity, $cuisine, $county;

		function Location($_data)
		{
			if (!is_array($_data) || $_data == null)
				return null;

			/*if (array_key_exists("addr:country", $_data))
				$this->country = $_data["addr:country"];*/
				
			if (array_key_exists("country", $_data))
				$this->country = $_data["country"];

			if (array_key_exists("county", $_data))
				$this->county = $_data["county"];

			if (array_key_exists("city", $_data))
				$this->city = $_data["city"];

			if (array_key_exists("postcode", $_data))
				$this->postcode = $_data["postcode"];

			if (array_key_exists("street", $_data))
				$this->street = $_data["street"];

			if (array_key_exists("housenumber", $_data))
				$this->housenumber = $_data["housenumber"];

			if (array_key_exists("amenity", $_data))
				$this->amenity = $_data["amenity"];

			if (array_key_exists("name", $_data))
				$this->name = $_data["name"];

			if (array_key_exists("cuisine", $_data))
				$this->cuisine = $_data["cuisine"];

			if (array_key_exists("osm_id", $_data))
				$this->osm_id = $_data["osm_id"];
		}

		/**
		 *  String location's representation.
		 *
		 *  @return string Location's short info in string format.
		 */

		function toString()
		{
			$s = "";

			if ($this->county != null)
				$s .= "County: " . $this->county . "; ";

			if ($this->country != null)
				$s .= "Country: " . $this->country . "; ";

			if ($this->city != null)
				$s .= "City: " . $this->city . "; ";

			if ($this->postcode != null)
				$s .= "Postcode: " . $this->postcode . "; ";

			if ($this->street != null)
				$s .= "Street: " . $this->street . "; ";

			if ($this->housenumber != null)
				$s .= "Housenumber: " . $this->housenumber . "; ";

			if ($this->amenity != null)
				$s .= "Amenity: " . $this->amenity . "; ";

			if ($this->name != null)
				$s .= "Name: " . $this->name . "; ";

			if ($this->cuisine != null)
				$s .= "Cuisine: " . $this->cuisine . "; ";

			if ($this->osm_id != null)
				$s .= "OSM ID: " . $this->osm_id . "; ";

			return "Location [" . $s . "]";
		}
	};

?>
