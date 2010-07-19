<?php
	require "../lib/Cloudmade.php";

	$client = new Client("BC9A493B41014CAABB98F0471D759707");
	
	# STATIC MAP TEST #1
	$marker = new Marker(array("image" => "http://cloudmade.com/images/layout/cloudmade-logo.png", "position" => new Point(51.477225, 0.0)));
	$path = array("point1" => new Point(51.477225, 0.0), "point2" => new Point(51.477225, 0.1), "point3" => new Point(51.477225, 0.2), "point4" => new Point(51.477225, 0.3));
	$map = $client->getStaticMap("600x500", new Point(51.477222, 0), 14, array("format" => "png", "style" => "1", "marker" => $marker, "path" => $path));

	$f = fopen("bar.png", "w");
		fwrite($f, $map);
	fclose($f);

	echo "<strong>StaticMapService test #1: </strong><br /><img src='bar.png' /><br />";
	
	# STATIC MAP TEST #2
	$marker1 = new Marker(array("image" => "http://cloudmade.com/images/layout/cloudmade-logo.png", "position" => new Point(51.477225, 0.0)));
	$marker2 = new Marker(array("image" => "http://cloudmade.com/images/layout/cloudmade-logo.png", "position" => new Point(51.477225, 0.001)));
	$path = new Path(array("point1" => new Point(51.477225, 0.0), "point2" => new Point(51.477225, 0.1), "point3" => new Point(51.477225, 0.2), "point4" => new Point(51.477225, 0.3)));
	$map = $client->getStaticMap("600x500", new Point(51.477222, 0), 14, array("format" => "jpg", "style" => "1", "marker" => array($marker1, $marker2), "path" => $path));

	$f = fopen("bar2.jpg", "w");
		fwrite($f, $map);
	fclose($f);

	echo "<strong>StaticMapService test #2: </strong><br /><img src='bar2.png' /><br />";
	
	# STATIC MAP TEST #3
	$marker = new Marker(array("image" => "http://cloudmade.com/images/layout/cloudmade-logo.png", "position" => new Point(51.477225, 0.0)));
	$path = new Path(array("point1" => new Point(51.477225, 0.0), "point2" => new Point(52.77225, 0.1)));
	$map = $client->getStaticMap("600x500", new Point(51.477222, 0), 14, array("format" => "png32", "style" => "1", "marker" => $marker, "path" => $path));

	$f = fopen("bar3.png", "w");
		fwrite($f, $map);
	fclose($f);

	echo "<strong>StaticMapService test #3: </strong><br /><img src='bar3.png' /><br />\n";
?>
