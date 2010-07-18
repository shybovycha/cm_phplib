<?php

	/**
	 *  @copyright Cloudmade, 2010
	 *  @license license.txt
	 */

	require_once "Cloudmade.php";

	/**
	 *  @see TileService
	 *  @package Cloudmade
	 *
	 *  Implements tile getting interface.
	 *
	 *  @property int $default_tile_size Default tile size.
	 *  @property int $default_style_id Default UI style ID.
	 */

	class TileService extends Service
	{
		var $default_tile_size, $default_style_id;

		/**
		 *  Constructor.
		 *
		 *  @param Client $_client Existing {@link Client} object.
		 *  @param string|null $_subdomain Service's subdomain (used for versioning).
		 *  @param array|null $_options Optional arguments, merged into array. Possible items are: "tile_size" and "style_id". They'll
		 *  change default values for $default_tile_size and $default_style_id accordingly.
		 */

		function TileService($_client, $_options = null, $_subdomain = null)
		{
			$subdomain = ($_subdomain == null) ? "tile" : $_subdomain;

			parent::Service($_client, $subdomain);

			$this->default_tile_size = ((is_array($_options) && array_key_exists("tile_size", $_options)) ? $_options["tile_size"] : 256);
			$this->default_style_id = ((is_array($_options) && array_key_exists("style_id", $_options)) ? $_options["style_id"] : 1);
		}

		/**
		 *  Convert latitude, longitude pair to tile coordinates.
		 *  @param int $_lat Latitude.
		 *  @param int $_lon Longitude.
		 *  @param int $_zoom Zoom level.
		 *  @return Point Returns tile coordinates as a {@link Point} object.
		 */

		function latlon2tilenums($_lat, $_lon, $_zoom)
		{
			$factor = pow(2, (floatval($_zoom) - 1)); // if zoom always >= 0, this willbe faster: 1 << intval($zoom)
			$lat = $this->radians($_lat);
			$lon = $this->radians($_lon);
			$x_tile = 1 + $lon / M_PI;
			$y_tile = 1 - log(tan($lat) + (1 / cos($lat))) / M_PI;

			return new Point(intval($x_tile * $factor), intval($y_tile * $factor));
		}

		/**
		 *  Convert tile coordinates pair to latitude, longitude.
		 *  @param int $_xtile X coordinate of the tile.
		 *  @param int $_ytile Y coordinate of the tile.
		 *  @param itn $_zoom Zoom level.
		 *  @return Point Returns latitude and longitude as a {@link Point} object.
		 */

		function tilenums2latlon($_xtile, $_ytile, $_zoom)
		{
			$factor = pow(2.0, floatval($_zoom));
			$lon = ($_xtile * 360 / $factor) - 180.0;
			$lat = atan(sinh(M_PI * (1 - 2 * $_ytile / $factor)));

			return new Point($this->degrees($lat), $lon);
		}

		/**
		 *  Utility function. Transforms degree value to radian one.
		 *
		 *  @param float $_degrees Degree value.
		 *  @return float Radian value.
		 */

		function radians($_degrees)
		{
			return M_PI * $_degrees / 180;
		}

		/**
		 *  Utility function. Converts radians to degrees.
		 *
		 *  @param float $_radians Radian value.
		 *  @return float Degree value.
		 */

		function degrees($_radians)
		{
			return $_radians * 180 / M_PI;
		}

		/**
		 *  Utility funciton. Converts longitude and zoom values to X tile number.
		 *
		 *  @param float $_lon Longitude.
		 *  @param int $_zoom Zoom level.
		 *  @return int X tile number (ID).
		 */

		function xtile($_lon, $_zoom)
		{
			$factor = pow(2, floatval($_zoom - 1));
			$xtile = 1 + $_lon / 180.0;

			return intval($xtile * $factor);
		}

		/**
		 *  Utility funciton. Converts latitude and zoom values to Y tile number.
		 *
		 *  @param float $_lat Latitude.
		 *  @param int $_zoom Zoom level.
		 *  @return int Y tile number (ID).
		 */

		function ytile($_lat, $_zoom)
		{
			$factor = pow(2, floatval($_zoom - 1));
			$lat = $this->radians($_lat);
			$ytile = 1 - log(tan($lat) + (1 / cos($lat))) / M_PI;

			return intval($ytile * $factor);
		}

		/**
		 *  Get tile with given x, y numbers and zoom
		 *
		 *  @param int $_xtile X tile number
		 *  @param int $_ytile Y tile number
		 *  @param int|null $_zoom Zoom level, on which tile is being requested
		 *  @param int|null $_style_id CloudMade's style id, if not given, default style is used (usually 1)
		 *  @param int|null $_tile_size size of tile, if not given the default 256 is used
		 *  @return string Returns Raw PNG data which could be saved to file (tile's image content).
		 */

		function getTileXY($_xtile, $_ytile, $_zoom = 1, $_style_id = null, $_tile_size = null)
		{
			$style_id = ($_style_id == null) ? $this->default_style_id : $_style_id;
			$tile_size = ($_tile_size == null) ? $this->default_tile_size : $_tile_size;

			return parent::connect("/" . strval($style_id) . "/" . strval($tile_size) . "/" . strval($_zoom) . "/" . strval($_xtile) . "/" . strval($_ytile) . ".png");
		}

		/**
		 * Get tile with given latitude, longitude and zoom.
		 *
		 *  @param float $_lat Latitude of requested tile
		 *  @param float $_lon Longitude of requested tile
		 *  @param int|null $_zoom Zoom level, on which tile is being requested
		 *  @param int|null $_style_id CloudMade's style id, if not given, default style is used (usually 1)
		 *  @param int|null $_tile_size size of tile, if not given the default 256 is used
		 *  @return string Returns Raw PNG data which could be saved to file.
		 */

		function getTile($_lat, $_lon, $_zoom, $_style_id = null, $_tile_size = null)
		{
			return $this->getTileXY($this->xtile($_lon, $_zoom), $this->ytile($_lat, $_zoom), $_zoom, $_style_id, $_tile_size);
		}
	};

?>
