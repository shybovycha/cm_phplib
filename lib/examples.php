<?php
	/**
	 * These short examples (or code snippets) should help you
	 * on getting started with Cloudmade's PHP API.
	 * 
	 * September 2009
	 * Artem Shubovych
	 */
	
	/**
	 * Let's start with getting tile from Cloudmade's server.
	 * First we should include Tile.php file into our script.
	 * It includes base class for working with tiles.
	 */
	
	require 'Tile.php';
	
	/**
	 * Next, we should connect to the map server with API key
	 * we were given after registration.
	 */
	
	$connection = new Connection('BC9A493B41014CAABB98F0471D759707');
	
	/**
	 * Now, we get tile with certain at latitude and longitude.
	 * Also, we can call this function with zoom, style ID and
	 * connection parameters given. 'Using this function we
	 * retrieve our tile source code.
	 */
	
	$tile = cm_get_tile($connection, 47.26117, 9.59882, 10, 1, 256);
   
	/**
	 * So far, so good. We've got our tile's binary text. To see
	 * it on the screen, we should save that data to a *.PNG file.
	 */
	$f = fopen('file.png', 'w+');
	fprintf($f, '%s', $tile);
	fclose($f);
	
	/**
	 * Now, let's use Geocoding API to find come places using
	 * two methods - find() and find_closest().
	 * 
	 * First of all, we should include Geocoding.php file
	 * to our script.
	 */
	
	require 'Geocoding.php';
	
	/**
	 * Though of the first example, we already got Connection object.
	 * So we don't need to create it again. But in your scripts
	 * this is critical - to use Cloudmade's services via PHP API
	 * (as much as using other APIs) you MUST create Connection
	 * object once.
	 * 
	 * So, we have to find some object, right? This is made simply with
	 * Cloudmade's PHP API. Just use find() function with such arguments:
	 * search query, showing capacity of results, number of results to 
	 * skip from the beginning of the list and connection. If you want
	 * some more options, look at the source code.
	 */
	
	$results = cm_find($connection, "Potsdamer Platz,Berlin,Germany", 10, 0); 
	
	/**
	 * Now we've got search results and we're going to store 'em to
	 * the $result variable. We don't need to see all the results we
	 * got. Let's see just one for our example.
	 */
	
	$result = $results->results[0];
	
	/**
	 * And let's display it on the screen:
	 */
	
	echo $result->properties_to_string() . '<br />' . $result->centroid->to_string() . '<hr />';
	
	/**
	 * Now, let's search another place using find_closest() function.
	 * This function finds the most comparible to the search query
	 * place. For example, if you know, where you are standing at
	 * the moment and you want to find closest pub to you (R = 50 km).
	 * Here is the line of code, which will help you to do that:
	 */
	
	$result = cm_find_closest($connection, 'pub', new Point(array(52.50939, 13.37638)));
	
	/**
	 * And here we've got only one result, so we don't need to filter it.
	 */
	
	echo $result->properties_to_string() . '<br />' . $result->centroid->to_string() . '<hr />';
	
	require_once 'Routing.php';
	
	$instructions = cm_route($connection, new Point(array(47.25976, 9.58423)), new Point(array(47.66117, 9.99882)));
	
	echo '<br />';
	for ($i = 0; $i < count($instructions); $i++)
		echo $instructions[$i]->instruction . '<br />';
?>
