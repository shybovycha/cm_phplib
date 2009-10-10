== Installation and getting started ==

 First of all you should sign-up for a CloudMade API key, by following this link, if you have not done it before.
 
 Next, get a copy of this API using Bazaar:
 
	$ bzr branch lp:~shybovycha/+junk/cm_phplib
	
 Then, install PHP5 and some extensions. Instructions are written below. If you have it done, just skip next lines.
 
 If you have not installed PHP, tou can do it like this:

	$ sudo apt-get install php5
 
 After you get an instance of PHP5 installed on your PC, you should install some extensions to get API
working. It can be done by running this command from terminal:

	$ sudo apt-get install php5-dev libcurl4-gnutls-dev libcurl3 libmagic-dev
 
And then:

	$ sudo pecl install pecl_http

After that, just enablt http.so module in your php.ini file:

* for CLI:

	$ sudo nano /etc/php5/cli/php.ini

* for Apache:
	
	$ sudo nano /etc/php5/apache2/php.ini

and add this line somewhere:

	extension = http.so

In case of using Apache, you also should copy http.so file to the Apache's modules/ dir:

	$ sudo cp /usr/lib/php5/20060613+lfs/http.so /usr/lib/apache2/modules

and restart Apache.

 When you have installed all these extensions, just unzip the archive or
clone repository version to any directory on your PC and run scripts via *php5* command.


== Get tile from CloudMade Tile Server ==

 Lets request the tile that contains the point with latitude = 47.26117, longitude = 9.59882 and zoom_level = 10.

	require 'Tile.php';
	
	$connection = new Connection('BC9A493B41014CAABB98F0471D759707');
	
	$tile = cm_get_tile($connection, 47.26117, 9.59882, 10, 1, 256);
   
	$f = fopen('file.png', 'w+');
	fprintf($f, '%s', $tile);
	fclose($f);
	
	
== Geocoding - find geoobjects like city, street or point of interests ==


 Lets find Potsdamer Platz in Berlin, Germany and also let's find the closest pub to the point with 
latitude = 51.66117, longitude = 13.37654.

	require 'Geocoding.php';
	
	$results = cm_find($connection, "Potsdamer Platz,Berlin,Germany", 10, 0); 
	
	$result = $results->results[0];
	
	echo $result->properties_to_string() . '<br />' . $result->centroid->to_string() . '<br />';
	
	$result = cm_find_closest($connection, 'pub', new Point(array(51.66117, 13.37654)));
	
	echo $result->properties_to_string() . '<br />' . $result->centroid->to_string() . '<br />';
	

== Routing - find route between two points ==

 Lets find the shortest route by car between two points.
 
	require_once 'Routing.php';
	
	$instructions = cm_route($connection, new Point(array(47.25976, 9.58423)), new Point(array(47.66117, 9.99882))); //$routing->route(new Point(array(47.25976, 9.58423)), new Point(array(47.66117, 9.99882)))->instruction();
	
	echo '<br />';
	for ($i = 0; $i < count($instructions); $i++)
		echo $instructions[$i]->instruction . '<br />';
