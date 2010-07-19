<?php
	require "../lib/Cloudmade.php";

	$client = new Client("BC9A493B41014CAABB98F0471D759707");
	
	# VECTOR STREAM TEST
	$bb = new BBox(array(new Point(-0.029055, 51.486895), new Point(-0.090424, 51.510023)));

	$tile2 = $client->getTileFromBBox($bb, "line");

	$f = fopen("foo.svgz", "w");
		fwrite($f, $tile2);
	fclose($f);

	echo "<strong>Tile from VectorStream (from BBox) test: </strong><br /><a href='foo.svg'>[FILE]</a><br /><br />";

	# VECTOR STREAM SECOND TEST
	$tile3 = $client->getTileFromCoords(47.26117, 9.59882, 15);

	$f = fopen("zaooza.svg", "w");
		fwrite($f, $tile3);
	fclose($f);

	echo "<strong>Tile from VectorStream (fromCoords) test: </strong><br /><a href='zaooza.svg'>[FILE]</a><br /><br />\n";
?>
