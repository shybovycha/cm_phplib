<?php
	require_once "PHPUnit/Framework.php";
	require_once "../lib/Cloudmade.php";
	require_once "MockConnection.php";

	class GeometryTest extends PHPUnit_Framework_TestCase
	{
		function test_point_creation()
		{
			$point = new Point(1, 3);
			$this->assertEquals($point->lat, 1);
			$this->assertEquals($point->lon, 3);

			$point = new Point(array(1, 3));
			$this->assertEquals($point->lat, 1);
			$this->assertEquals($point->lon, 3);
		}

		function test_point_equality()
		{
			$point1 = new Point(0, 1);
			$point2 = new Point(array(0 * 7, -3 + 4));
			$this->assertEquals($point1, $point2);

			$point3 = new Point(1.0, 0.1);
			$this->assertFalse($point3 == $point1);
		}

		function test_point_latlon()
		{
			$point1 = new Point(1, 2);
			$this->assertEquals($point1->toLatLon(), "1,2");
		}

		function test_line_creation()
		{
			$line = new Line(array(new Point(3.7, -4.5), new Point(0.1, 2.1)));
			$this->assertEquals(count($line->points), 2);
			$this->assertEquals($line->points[0]->lat, 3.7);
			$this->assertEquals($line->points[0]->lon, -4.5);
			$this->assertEquals($line->points[1]->lat, 0.1);
			$this->assertEquals($line->points[1]->lon, 2.1);

			$line = new Line(array(3.7, -4.5, 0.1, 2.1, 3.14, 17.0, 2.72, 10.3));
			$this->assertEquals(count($line->points), 4);
		}

		function test_multiline_creation()
		{
			$coords = array(array(2.72, 10.3), array(3.7, -4.5, 0.1, 2.1, 3.14, 17.0));
			$ml = new Multiline($coords);
			$this->assertEquals(count($ml->lines), 2);
			$this->assertEquals(count($ml->lines[0]->points), 1);
			$this->assertEquals(count($ml->lines[1]->points), 3);
			$this->assertEquals($ml->lines[1]->points[1]->lon, 2.1);
		}

		function test_polygon_creation()
		{
			$coords = array(array(0.2, 35.2, 4.3, 45.1, 5.7, 11.2), array(1.1, 33.2, 5.3, 22.2));
			$poly = new Polygon($coords);
			$this->assertFalse($poly->border_line == null);
			$this->assertFalse($poly->holes == null);
			$this->assertEquals(count($poly->border_line->points), 3);
			$this->assertEquals(count($poly->holes), 1);
		}

		function test_multipolygon_creation()
		{
			$coords = array(array(array(0.2, 35.2, 4.3, 45.1)), array(array(1.1, 33.2, 5.3, 22.2)));
			$mp = new MultiPolygon($coords);
			$this->assertEquals(count($mp->polygons), 2);
			$this->assertEquals(count($mp->polygons[0]->holes), 0);
			$this->assertEquals(count($mp->polygons[1]->holes), 0);
		}

		function test_bbox_creation()
		{
			$coords = array(array(0.2, 35.2), array(4.3, 45.1));
			$point1 = new Point($coords[0]);
			$point2 = new Point($coords[1]);
			$bbox = new BBox(array($point1, $point2));
			$bbox2 = new BBox($coords);

			$this->assertEquals(count($bbox->points), 2);
			$this->assertEquals($bbox->points[0], $point1);
			$this->assertEquals($bbox->points[1], $point2);

			$this->assertEquals($bbox, $bbox2);
			$this->assertEquals(count($bbox2->points), 2);
			$this->assertEquals($bbox2->points[0], $point1);
			$this->assertEquals($bbox2->points[1], $point2);
		}
	}
?>
