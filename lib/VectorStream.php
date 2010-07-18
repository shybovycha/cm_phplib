<?php

	/**
	 *  @copyright Cloudmade, 2010
	 *  @license license.txt
	 */

    require_once 'Cloudmade.php';

	/**
	 *  @see VectorStreamService
	 *  @package Cloudmade
	 *
	 *  Cloudmade's Vector Streaming Service interface class.
	 */

    class VectorStreamService extends Service
    {
		/**
		 *  Constructor.
		 *
		 *  @param Connection $_client Existing {@link Connection} object.
		 *  @param string|null $_subdomain Subdomain string. Optional. Defaults to null. Use for service version changes only.
		 */

    	function VectorStreamService($_client, $_subdomain = null)
    	{
    		$subdomain = ($_subdomain == null) ? "alpha.vectors" : $_subdomain;

            parent::Service($_client, $subdomain);
    	}

		/**
		 *  Gets tile, using BBox argument.
		 *
		 *  @param BBox $_bbox Bounding box, you want tile get from.
		 *  @param string|null $_datatype The type of response. Supported types: svg, svgz. Optional.
		 *  @param array|null $_options Optional arguments hash. Possible values are:
		 *  <ul>
		 *  <li><b>viewport</b> Height and width of view port, separated by 'x' (ASCII code 120). Defaults to 800x600 </li>
		 *	<li><b>styleid</b> Style id to be used for filtering and styling data. Style id should be obtained on Style Editor site. There's no default value, meaning all the data will be returned as it is with minimal styling </li>
		 *  <li><b>zoom</b> Zoom level of current view. This affects styling and filtering elements of the map. If zoom is not present, but viewport is given, than zoom is automatically derived from size of bbox and view port. If both viewport and zoom are provided, zoom will be used. zoom doesn't have a lot of meaning if used without styleId </li>
		 *  <li><b>coords</b> What type of coordinates will be used in SVG document. Currently available options - rel and abs (meaning "relative" and "absolute", correspondingly). Defaults to abs  </li>
		 *  <li><b>precision</b> This option controls output of the style-filtered elements. When set to hide this means that all values that should not be visible are opaque, but still present in the resulting SVG. When set to remove  all elements that should not be visible are removed from resulting document. Defaults to remove </li>
		 *  <li><b>unused</b> Precision of coordinates. Determines how much digits after decimal point should be provided. Should be in range 0..15. Defaults to 15 </li>
		 *  <li><b>exclude</b> This parameter can be used if you want to exclude data from given bounding box (or even several bounding boxes) from response</li>
		 *  <li><b>clipped</b> Controls the way geometry is output in the result. If clipping is enabled, then geometries will be clipped to fit into bounding box/tile. Possible values: true, false. Default: false </li>
		 *  <li><b>coastlines</b> Controls whether the resulting SVG should contain information about coastlines. Can be either true or false. Defaults to false  </li>
		 *  </ul>
		 *
		 *  @return string File content.
		 *  @see VectorStreamService_getTileFromBBox
		 */

    	function getTileFromBBox($_bbox, $_datatype = null, $_options = null)
    	{
			if ($_bbox == null)
    		    return null;

    		if ($_options != null && !is_array($_options))
    		    return null;

    		if ($_datatype == null)
    		    $_datatype = "*";

    		$url = "/" . $_bbox->toUrl() . "/";

			if (is_string($_datatype))
			{
				$url .= $_datatype;
			} else
			if (is_array($_datatype) && is_string($_datatype[0]))
			{
				$url .= $_datatype[0];
			}

    		if (is_array($_options))
    		{
    			$url .= "?";

    			# Height and width of view port in pixels, separated by x (ASCII code 120)
    			# Defaults to 800x600
    		    if (array_key_exists("viewport", $_options) && is_array($_options["viewport"]))
    		        $url .= "viewport=" . $_options["viewport"][0] . "x" . $_options["viewport"][1] . "&";

    		    if (array_key_exists("styleid", $_options))
    		        $url .= "styleid=" . $_options["styleid"] . "&";

    		    if (array_key_exists("zoom", $_options))
                    $url .= "zoom=" . $_options["zoom"] . "&";

                # Defines what type of coordinates will be used in SVG document
                # Currently available options: rel and abs
                if (array_key_exists("coords", $_options))
                    $url .= "coords=" . $_options["coords"] . "&";

                # Determines how precise the resulting co-ordinates should be in decimal places
                # Should be in range [0, 15]
                if (array_key_exists("precision", $_options))
                    $url .= "precision=" . $_options["precision"] . "&";

                # This option controls output of the style-filtered elements
                # When set to hide this means that all values that should not be visible are opaque
                # but still present in the resulting SVG. When set to remove all elements that should
                # not be visible are removed from resulting document. Defaults to remove.
                # Use hide only when you need all the data from the bounding box (i.e. offline maps).
                if (array_key_exists("unused", $_options))
                    $url .= "unused=" . $_options["unused"] . "&";

                # This parameter can be used if you want to exclude data from given bounding box
                # (or even several bounding boxes) from response
                if (array_key_exists("exclude", $_options))
                {
                	if (is_object($_options["exclude"]))
					{
                        $url .= "exclude=" . $_options["exclude"]->toUrl() . "&";
					} else
                    if (is_array($_options["exclude"]) && is_object($_options["exclude"][0]))
                    {
                        foreach ($_options["exclude"] as $i)
                            $url .= "eclude=" . $i->toUrl() . "&";
                    }
                }

                # Controls the way geometry is output in the result
                # If clipping is enabled, then geometries will be clipped to fit into bounding box/tile
                if (array_key_exists("clipped", $_options))
                    $url .= "clipped=" . boolval($_options["clipped"]) . "&";

                # Controls whether the resulting SVG should contain information about coastlines.
                # Can be either true or false. Defaults to false
                if (array_key_exists("coastlines", $_options))
                    $url .= "coastlines=" . $_options["coastlines"] . "&";
    		}

    		$url = rtrim($url, "/?.,&=: ") . "/";

            return parent::connect($url);
    	}

		/**
		 *  The same as {@link VectorStreamService_getTileFromBBox}, but uses <i>lat</i>, <i>lon</i> and <i>zoom</i> arguments instead of one BBox.
		 *
		 *  @param float $_lat Latitude
		 *  @param float $_lon Longitude
		 *  @param int $_zoom Zoom level. Must be the power of 2.
		 *  @param array $_options Optional arguments hash. Read {@link VectorStreamService_getTileFromBBox} for details.
		 *  @return string File content.
		 */

		function getTileFromCoords($_lat, $_lon, $_zoom, $_options = null)
		{
			$zoom = ($_zoom <= 0) ? 1 : $_zoom;

			$xtile = floor((($_lon + 180) / 360) * pow(2, $zoom));
			$ytile = floor((1 - log(tan(deg2rad($_lat)) + 1 / cos(deg2rad($_lat))) / M_PI) / 2 * pow(2, $zoom));

			if (is_numeric($_options["size"]))
				$size = $_options["size"]; else
					$size = 256;

			if (is_numeric($_options["style"]))
				$style = $_options["style"]; else
					$style = 1;

			if (is_string($_options["type"]))
				$type = $_options["type"]; else
					$type = "svg";

			$url = "/" . implode("/", array($style, $size, $zoom, $xtile, $ytile)) . "." . $type . "?";

			if (is_array($_options))
			{
				# Optional arguments. Look through getTileFromBBox for details
				if (array_key_exists("coords", $_options))
					$url .= "coords=" . $_options["coords"] . "&";

				if (array_key_exists("precision", $_options))
					$url .= "precision=" . $_options["precision"] . "&";

				if (array_key_exists("unused", $_options))
					$url .= "unused=" . $_options["unused"] . "&";

				if (array_key_exists("exclude", $_options))
				{
					if (is_object($_options["exclude"]))
					{
						$url .= "exclude=" . $_options["exclude"]->toUrl() . "&";
					} else
					if (is_array($_options["exclude"]) && is_object($_options["exclude"][0]))
					{
						foreach ($_options["exclude"] as $i)
							$url .= "eclude=" . $i->toUrl() . "&";
					}
				}

				if (array_key_exists("clipped", $_options))
					$url .= "clipped=" . boolval($_options["clipped"]) . "&";

				if (array_key_exists("coastlines", $_options))
					$url .= "coastlines=" . boolval($_options["coastlines"]);
			}

			$url = rtrim($url, "?&,=/. ");

			return parent::connect($url);
		}
    };

?>
