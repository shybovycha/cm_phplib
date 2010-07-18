<?php
	require_once "PHPUnit/Framework.php";
	require_once "../lib/Cloudmade.php";
	require_once "MockConnection.php";

	class StaticmapTest extends PHPUnit_Framework_TestCase
	{
		var $connection, $maps;

		function StaticmapTest()
		{
			$this->connection = new MockConnection('FAKE_API_KEY', 'fake.url');
			$this->maps = new StaticMapsService($this->connection);
		}

		function test_get_map()
		{
			$this->connection->set_data("SVG file content");

			$marker = new Marker(array("image" => "http://cloudmade.com/images/layout/cloudmade-logo.png", "position" => new Point(51.477225, 0.0)));
			$path = array("point1" => new Point(51.477225, 0.0), "point2" => new Point(51.477225, 0.1), "point3" => new Point(51.477225, 0.2), "point4" => new Point(51.477225, 0.3));
			$map = $this->maps->getMap("600x500", new Point(51.477222, 0), 14, array("format" => "png", "style" => "1", "marker" => $marker, "path" => $path));

			$this->assertEquals($this->connection->request, 'http://staticmaps.fake.url/FAKE_API_KEY/staticmap?size=600x500&center=51.477222,0&zoom=14&format=png&styleid=1&marker=url:http://cloudmade.com/images/layout/cloudmade-logo.png|51.477225,0&51.477225,0&51.477225,0.1&51.477225,0.2&51.477225,0.3');
			$this->assertEquals($map, $this->connection->return_data);
		}
	}
?>
