<?php

/**
 * @package Geocoding
 * @author Artem Shybovych
 */

require_once "Geometry.php";

if (!isset($_TEST_))
	require_once "Connection.php";

/**
 * Class that is responsible for geocoding services of Cloudmade
 * 
 * @param string $sub_domain Subdomain of CloudMade's geocoding service
 * @param Connection $connection Connection object to be used by CloudMade's
 * geocoding service
 */

class Geocoding {
	var $sub_domain, $connection;
	
	/**
	 * Constructor
	 */
	
	function GeoCoding($_connection) {
        $this->sub_domain = 'geocoding';
        $this->connection = $_connection;
    }

	/**
	 * Find objects that match given query
     * Query should be in format [POI],[House Number],[Street],[City],[County]
     * like "Potsdamer Platz, Berlin, Germany". 
     * Also supports "near" in queries, e.g. "hotel near Potsdamer Platz, Berlin, Germany"
     * 
     * @param string $query: Query by which objects should be searched.
     * @param int $results: How many results should be returned.
     * @param int $skip: Number of results to skip from beginning.
     * @param BoundingBox $bbox: Bounding box in which objects should be searched.
     * @param bool $bbox_only: If set to False, results will be searched in the
     * whole world if there are no results for a given bbox.
     * @param bool $return_geometry: If specified, adds geometry in returned
     * results. Defaults to True.
     * @param bool $return_location: If specified, adds location information in returned
     * results. Defaults to False.
     * @return GeoResults objects.
	 */
    
    function find($query, $results = 10, $skip = 0, $bbox = null,
             $bbox_only = true, $return_geometry = true, $return_location = false) {
        
        $params = array('return_geometry' => $return_geometry, 
						'return_location' => $return_location,
						'bbox_only' => $bbox_only, 
						'results' => $results, 
						'skip' => $skip);
                  
        if (isset($bbox))
			$params['bbox'] = $bbox->to_string();
			
		$uri = '/geocoding/find/' . rawurlencode($query) . '.js?' . http_build_query($params);
		
		return $this->_call_service($uri);
    }

	/**
	 * Find closest object to a given point
	 *
     * @note For a list of available object types, see:
     * http://developers.cloudmade.com/projects/show/geocoding-http-api
	 *
     * @param string $object_type Type of object, that should be searched.
     * @param Point $point Closes object to this point will be searched.
     * @param bool $return_geometry If specified, adds geometry in returned
     * result. Defaults to True.
     * @param bool $return_location If specified, adds location information in returned
     * results. Defaults to False.
     * 
     * @return Object that was found.
	 */
	
    function find_closest($object_type, $point, $return_geometry = true, $return_location = false) {
        $params = array('return_geometry' => $return_geometry, 
						'return_location' => $return_location);
						
		$uri = '/geocoding/closest/' . rawurlencode($object_type) . '/' . $point->to_string() . '.js?' . http_build_query($params);
		
		try {
			$r = $this->_call_service($uri);
			return $r->results[0];
        } catch(Exception $e) {
            echo 'Object of type "' . $object_type . '" was not found in radius of 50 km from ' . strval($point) . ' point';
            
            return null;
        }
    }

	/**
	 * Make the call to CloudMade's service using underlying connection
	 *
     * @note This is an internal method and shouldn't be used directly
	 *
     * @param string $uri Request string
     * @return List of L{cloudmade.geocoging.GeoResult} objects that
     * were produced after processing request.
	 */
	
    function _call_service($uri) {
		$raw = $this->connection->call_service($uri, $this->sub_domain);
		
		$result = new GeoResults(json_decode($raw, true));
        
        return $result;
    }
}

/**
 * Description of Location
 *
 */

class Location {
	var $road, $city, $country, $county, $postcode;
	
    function Location($data) {
		$this->road = $data['road'];
		$this->city = $data['city'];
		$this->county = $data['county'];
		$this->country = $data['country'];
		$this->postcode = $data['postcode'];
    }
}

/**
 * Description of GeoResult
 *
 */

class GeoResult {
	var $id, $geometry, $centroid, $bounds, $properties, $location;
	
    function GeoResult($data) {
		$this->id = $data['id'];
		$this->geometry = Geometry($data['geometry']);
		$this->centroid = Geometry($data['centroid']);
		$this->bounds = BoundingBox::from_coordinates($data['bounds']);
		
		$this->properties = $data['properties'];
		
		if (array_key_exists('location', $data))
			$this->location = new Location($data['location']); else
				$this->location = null;
    }
    
    function properties_to_string() {
		$res = '';
		
		foreach ($this->properties as $i)
			$res = $res . ' ' . $i;
			
		return $res;
	}
}

/**
 * Description of GeoResults
 *
 */

class GeoResults {
	var $found, $results, $bounds;
	
    function GeoResults($data) {
		$this->found = intval($data['found']);
		
		for ($i = 0; $i < count($data['features']); $i++)
			$this->results[] = new GeoResult($data['features'][$i]);
			
		if (array_key_exists('bounds', $data)) {
			$this->bounds = BoundingBox::from_coordinates($data['bounds']);
		} else
			$this->bounds = null;
	}
}

function cm_find($connection, $query, $results = 10, $skip = 0, $bbox = null, $bbox_only = true,
         $return_geometry = true) {
			 
	$geocodingobj = new Geocoding($connection);
	
    return $geocodingobj->find($query, $results, $skip, $bbox, $bbox_only, $return_geometry);
}

function cm_find_closest($connection, $object_type, $point, $return_geometry = true) {
	$geocodingobj = new Geocoding($connection);
	
    return $geocodingobj->find_closest($object_type, $point, $return_geometry, $return_location);
}

?>
