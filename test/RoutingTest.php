<?php
	require_once "PHPUnit/Framework.php";
	require_once "../lib/Cloudmade.php";
	require_once "MockConnection.php";

	class RoutingTest extends PHPUnit_Framework_TestCase
	{
		var $connection;

		function RoutingTest()
		{
			$this->connection = new MockConnection('FAKE_API_KEY', 'fake.url');
			$this->routing = new RoutingService($this->connection);
		}

		function test_empty_routing()
		{
			$data = '{}';
			$transits = array(new Point(51.22, 4.41), new Point(51.2, 4.41));
			$this->connection->set_data($data);
			$this->setExpectedException('Exception');
			$route = $this->routing->route(new Point(51.22545, 4.40730), new Point(51.23, 4.42), $transits, 'car', 'shortest');
		}

		function test_routing()
		{
			$data = '{"status":0,"route_instructions":[["Head south on Generaal Belliardstraat",16,0,2,"16 m","S",176.2],
				["Turn right at Falconrui",149,1,18,"0.1 km","W",262.8,"TR",86.6],["Turn right at Falconplein",347,3,42,"0.3 km","N",345.9,"TR",83.0],
				["Turn right at Huikstraat",212,9,25,"0.2 km","SW",243.8,"TR",79.2],["Turn left at Sint-Paulusstraat",12,14,1,"12 m","SE",145.4,"TL",306.7],
				["Continue on Minderbroedersrui",303,15,18,"0.3 km","SE",152.4,"C",7.0],["Turn left at Kipdorp",337,22,40,"0.3 km","E",103.9,"TL",304.7],
				["Turn right at Sint-Jacobstraat",339,28,41,"0.3 km","S",196.2,"TR",94.1],["Turn right at Lange Nieuwstraat",297,33,71,"0.3 km","W",266.3,"TR",70.2],
				["Turn left at Sint-Katelijnevest",173,40,10,"0.2 km","S",182.4,"TL",259.4],["Slight left",21,46,1,"21 m","SE",156.0,"TSLL",331.3],
				["Continue on Meir",22,49,1,"22 m","SE",142.9,"C",351.3],["Slight right at Huidevettersstraat",266,51,16,"0.3 km","S",180.0,"TSLR",37.1],
				["Continue on Lange Gasthuisstraat",349,66,21,"0.3 km","S",167.6,"C",350.1],["Slight left at Sint-Jorispoort",192,71,12,"0.2 km","E",109.7,"TSLL",314.9],
				["Slight right at Mechelsesteenweg",96,74,6,"96 m","S",171.9,"TSLR",37.1],["Continue",59,76,3,"59 m","S",179.2,"C",6.8],
				["Continue on N1\/Mechelsesteenweg",754,79,39,"0.8 km","S",172.5,"C",353.8],
				["Turn right at Van Schoonbekestraat",367,91,44,"0.4 km","SW",247.0,"TR",88.6],["Turn left at Schulstraat",234,95,28,"0.2 km","SE",124.9,"TL",269.3],
				["Turn left at Harmoniestraat",3418,97,410,"3.4 km","NE",38.8,"TL",242.5],["Continue on Ellermanstraat",47,101,3,"47 m","W",267.9,"C",0.0]],
				"route_summary":{"total_time":852,"transit_points":[["Sint-Jacobstraat",51.22006,4.40965],["Harmoniestraat",51.20047,4.40913]],
				"total_distance":8010,"end_point":"Ellermanstraat","start_point":"Generaal Belliardstraat"},
				"route_geometry":[[51.22545,4.40728],[51.2253,4.4073],[51.22515,4.40533],[51.22514,4.40518],[51.22565,4.40498],
				[51.22651,4.40514],[51.22665,4.40511],[51.22652,4.40506],[51.2256,4.40479],[51.22511,4.40501],[51.22505,4.40481],
				[51.22503,4.40477],[51.22456,4.40388],[51.22406,4.40325],[51.22371,4.40307],[51.22362,4.40317],[51.22356,4.40321],
				[51.2226,4.40425],[51.22182,4.40501],[51.22179,4.40502],[51.22145,4.40507],[51.22141,4.40509],[51.22125,4.40519],
				[51.221,4.40681],[51.22096,4.40709],[51.22088,4.40769],[51.22087,4.40779],[51.22073,4.40921],[51.22064,4.40992],
				[51.21966,4.40947],[51.21966,4.40947],[51.22064,4.40992],[51.22006,4.40965],[51.21966,4.40947],[51.21965,4.40928],
				[51.21974,4.40814],[51.21981,4.40735],[51.21982,4.40726],[51.21989,4.40628],[51.21997,4.40571],[51.22003,4.40526],
				[51.21997,4.40526],[51.21923,4.40516],[51.21912,4.40509],[51.21893,4.40505],[51.2186,4.40503],[51.21849,4.40499],
				[51.21847,4.40501],[51.21838,4.40509],[51.21833,4.40514],[51.21826,4.40525],[51.21817,4.40533],[51.21809,4.40534],
				[51.2178,4.40533],[51.21752,4.40533],[51.21723,4.40537],[51.2169,4.40543],[51.2167,4.40543],[51.21655,4.4054],
				[51.21644,4.40534],[51.21638,4.40529],[51.2163,4.40516],[51.21625,4.40497],[51.21617,4.40483],[51.21616,4.40481],
				[51.2161,4.40478],[51.21596,4.40482],[51.21514,4.40511],[51.21402,4.40501],[51.21381,4.405],[51.21304,4.40499],
				[51.21286,4.40512],[51.21244,4.40699],[51.21227,4.40744],[51.21213,4.4075],[51.21142,4.40766],[51.21127,4.40768],
				[51.21112,4.40768],[51.21097,4.4077],[51.21074,4.40771],[51.20968,4.40793],[51.20914,4.40825],[51.20869,4.40857],
				[51.2084,4.40887],[51.20829,4.40894],[51.20695,4.40973],[51.20672,4.4098],[51.20644,4.40989],[51.20618,4.40991],
				[51.2059,4.40989],[51.20565,4.40998],[51.20432,4.41081],[51.2039,4.40924],[51.20382,4.40898],[51.20376,4.40884],
				[51.20218,4.40704],[51.20162,4.40833],[51.20097,4.40976],[51.20053,4.40921],[51.20097,4.40976],[51.22982,4.42068],
				[51.22981,4.42001],[51.22981,4.42001]],"version":"0.3"}';

			$this->connection->set_data($data);
			$transits = array(new Point(51.22, 4.41), new Point(51.2, 4.41));
			$route = $this->routing->route(new Point(51.22545, 4.40730), new Point(51.23, 4.42), $transits, 'car', 'shortest');

			$this->assertEquals($route->version, 0.3);
			$this->assertEquals($route->summary->total_time, 852);
			$this->assertEquals($route->summary->total_distance, 8010);
			$this->assertEquals($route->summary->end_point, 'Ellermanstraat');
			$this->assertEquals($route->summary->start_point, 'Generaal Belliardstraat');
		}
	}
?>
