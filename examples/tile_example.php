<?php
	require "../lib/Cloudmade.php";

	$client = new Client("BC9A493B41014CAABB98F0471D759707");
	
		# TILE TEST
	$tile = $client->getTile(47.26117, 9.59882, 15);

	$f = fopen("moo.png", "w");
		fwrite($f, $tile);
	fclose($f);

	echo "<strong>Tile test: </strong><br /><img src='moo.png' /><br /><br />\n";
?>
