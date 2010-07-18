<?php
	require_once "PHPUnit/Framework.php";
	require_once "../lib/Cloudmade.php";
	require_once "MockConnection.php";

	class TileTest extends PHPUnit_Framework_TestCase
	{
		var $connection, $tiles;

		function TileTest()
		{
			$this->connection = new MockConnection('FAKE_API_KEY', 'fake.url');
			$this->tiles = new TileService($this->connection);
		}

		function test_latlon2tilenums()
		{
			$point = $this->tiles->latlon2tilenums(11.1, 34.5, 15);
			$this->assertEquals($point->lat, 19524);
			$this->assertEquals($point->lon, 15367);
		}

		function test_tilenums2latlon()
		{
			$point = $this->tiles->tilenums2latlon(19524, 15367, 15);
			$this->assertEquals(intval($point->lat), 11);
			$this->assertEquals(intval($point->lon), 34);
		}

		function test_get_tile()
		{
			$this->connection->set_data("PNG file content");
			$png = $this->tiles->getTile(11.1, 34.5, 15);
			$this->assertEquals($this->connection->request, 'http://tile.fake.url/FAKE_API_KEY/1/256/15/19524/15367.png');
			$this->assertEquals($png, $this->connection->return_data);
		}
	}
?>
