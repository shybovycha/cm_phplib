<?php
	require "Cloudmade.php";

	$client = new Client("BC9A493B41014CAABB98F0471D759707");

	# GEOCODING TEST
	# With visualization
	$bb = new BBox(array(new Point(52.46882,13.38046), new Point(52.50518,13.46914)));
	$a = $client->find(array("bbox" => $bb, "object_type" => "hotel", "visualize" => "true"));
	echo "<strong>Geocoding 'find' test (visualized): </strong>" . $a . "<br /><br />";

	# Without "query" argument
	$bb = new BBox(array(new Point(52.46882, 13.38046), new Point(52.50518,13.46914)));
	$a = $client->find(array("bbox" => $bb, "object_type" => "hotel"));
	echo "<strong>Geocoding 'find' test (w/o query): </strong>" . $a->results[0]->toString() . "<br /><br />";

	# With "query" argument
	$a = $client->find(array("query" => "Hermannplatz, Berlin", "return_location" => "true"));
	echo "<strong>Geocoding 'find' test (with query): </strong>" . $a->results[0]->toString() . "<br /><br />";
?>