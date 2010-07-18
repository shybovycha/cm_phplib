<?php
	require_once "PHPUnit/Framework.php";
	require_once "../lib/Cloudmade.php";
	require_once "MockConnection.php";

	class StaticmapTest extends PHPUnit_Framework_TestCase
	{
		var $connection, $tiles;

		function StaticmapTest()
		{
			$this->connection = new MockConnection('FAKE_API_KEY', 'fake.url');
			$this->tiles = new VectorStreamService($this->connection);
		}

		function test_get_map()
		{
			$this->connection->set_data("SVGZ file content");

			$bb = new BBox(array(new Point(-0.029055, 51.486895), new Point(-0.090424, 51.510023)));
			$tile = $this->tiles->getTileFromBBox($bb, "line");

			$this->assertEquals($this->connection->request, 'http://alpha.vectors.fake.url/FAKE_API_KEY/-0.029055,51.486895,-0.090424,51.510023/line/');
			$this->assertEquals($tile, $this->connection->return_data);
		}
	}
?>
