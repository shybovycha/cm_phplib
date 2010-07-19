<?php
	require "../lib/Cloudmade.php";

	$client = new Client("BC9A493B41014CAABB98F0471D759707");
	
	# ROUTING TEST
	echo "<strong>Rouing test: </strong>" . $client->route(new Point(47.25976, 9.58423), new Point(47.66117, 9.99882), null, "bicycle")->toString() . "<br /><br />\n";
?>
