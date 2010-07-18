<?php

	/**
	 *  @copyright Cloudmade, 2010
	 *  @license license.txt
	 */

	require_once "Cloudmade.php";

	/**
	 *  @see StaticMapsService
	 *  @package Cloudmade
	 *
	 *  Service, allowing you to get static map tile image with some marks on it.
	 */

	class StaticMapsService extends Service
	{
		/**
		 *  Constructor.
		 *
		 *  @param Connection $_client Existing {@link Connection} object
		 *  @param string|null $_subdomain Sub-domain string. Defaults to null. Use this for service versioning only.
		 */

		function StaticMapsService($_client, $_subdomain = null)
    	{
    		$subdomain = ($_subdomain == null) ? "staticmaps" : $_subdomain;

            parent::Service($_client, $subdomain);
    	}

		/**
		 *  Map getter; the main method in this class (except constructor). Possible forms are: <i>getMap(BBox)</i> and <i>getMap(center, zoom)</i>
		 *
		 *  @param int $_size Map tile size. Should be the power of 2.
		 *  @param BBox|Point $_arg0 Either {@link BBox} or {@link Point} object.
		 *  @param int|null $_arg1 <i>"zoom"</i> value if the first argument is {@link Point} object or null if not.
		 *  @param array|null $_options Optional additional arguments. Possible keys are:
		 *  <ul>
		 *    <li>
		 *      <b>format</b>
		 *      The image format of the map. Can be one of png for 8-bit PNG images, png32 for 32-bit PNG images, jpg for JPEG images or gif for GIF images. Defaults to png.
		 *    </li>
		 *    <li>
		 *      <b>style</b>
		 *      Style id to be used for displaying map. Defaults to 1.
		 *    </li>
		 *    <li>
		 *      <b>marker</b>
		 *      Sets markers on the map. Possible values are: {@link Marker} object, {@link Marker} objects array or array of data, needed to generate {@link Marker} object. In the last case, you can pass an array of arrays of data needed.
		 *    </li>
		 *    <li>
		 *      <b>path</b>
		 *      Sets paths on the map image. Possible values are: {@link Path} object, {@link Path} objects array or array of data, needed to create a path. You can pass array of arrays of data, if needed.
		 *    </li>
		 *  </ul>
		 * 
		 */

		function getMap($_size, $_arg0, $_arg1 = null, $_options = null)
		{
			if ($_options != null && !is_array($_options))
    		    return null;

			$_size = "size=" . (($_size == null || !is_string($_size)) ? "800x600" : $_size) . "&";

			if (is_object($_arg0) && $_arg1 == null)
				$coords = "bbox=" . $_arg0->toUrl() . "&"; else
			if (is_object($_arg0) && !is_null($_arg1))
				$coords = "center=" . $_arg0->toUrl() . "&zoom=" . strval($_arg1) . "&";

			$url = "/staticmap?" . $_size . $coords;

			foreach ($_options as $k => $v)
			{
				if ($k == "format" && is_string($v))
				{
					$url .= "format=" . $v . "&";
				} else
				if ($k == "style" && is_numeric($v))
				{
					$url .= "styleid=" . strval($v) . "&";
				} else
				if (strpos("marker", $k) > -1)
				{
					if (is_object($v))
					{
						$url .= $v->toUrl() . "&";
					} else
					if (is_array($v))
					{
						if (is_object(current($v)))
						{
							foreach ($v as $j)
							{
								$url .= $j->toUrl() . "&";
							}
						} else
						if (is_array(current($v)))
						{
							foreach ($v as $j)
							{
								$marker = new Marker($j);
								$url .= $marker->toUrl() . "&";
							}
						}
					}
				} else
				if (strpos("path", $k) > -1)
				{
					if (is_object($v))
					{
						$url .= $v->toUrl() . "&";
					} else
					if (is_array($v) && count($v) > 0)
					{
						if (is_object(current($v)))
						{
							foreach ($v as $j)
							{
								$url .= $j->toUrl() . "&";
							}
						} else
						if (is_array(current($v)))
						{
							foreach ($v as $j)
							{
								$p = new Path($j);
								$url .= $j->toUrl() . "&";
							}
						}
					}
				}
			}

			$url = trim($url, "&|, ");

			return parent::connect($url);
		}
	};

	/**
	 *  @see Marker
	 *  @package Cloudmade
	 *
	 *  Class, describing a single marker on a static map tile.
	 *
	 *  @property int $size Defines the size of the marker. You can choose between <i>small</i>, <i>mid</i> and <i>big</i>. Defaults to <i>mid</i>.
	 *  @property string|null $label Symbol to be used for icon. The symbol itself can be either a letter or a number in the range from 1 to 99. Note that letters will be uppercased even if you pass lowercase. If you don't provide any value for this option, the label will be empty .
	 *  @property string|null $color The color of background of the label. Here's the list of available colors: red, lavender, lightblue, darkblue, green, grey, orange and white. If you omit this option, the label will be in default color (which is yellow).
	 *  @property float $opacity Use this to adjust transparency level. 1 is for completely opaque, 0.0 is for completely transparent (invisible). Defaults to 1.0.
	 *  @property string|null $image You can specify custom marker by simply passing <i>image</i> option which will contain URL of the marker.
	 *  @property Point|array|string $position Marker's global coordinates (in the lat-lon format). Could be string-value, {@link Point} object or lat-lon floats array.
	 */

	class Marker
	{
		var $size, $label, $color, $opacity, $image, $position;

		/**
		 *  Constructor.
		 *
		 *  @param array $_data Arguments' hash. Possible keys are described in {@link Marker} class property list.
		 */

		function Marker($_data)
		{
			if (is_null($_data["position"]) || !is_array($_data))
				return null;

			if (is_object($_data["position"]))
				$this->position = $_data["position"]->toUrl(); else
			if (is_string($_data["position"]))
				$this->position = $_data["position"]; else
			if (is_array($_data["position"]) && count($_data["position"]) == 2 && is_numeric($_data["position"][0]) && is_numeric($_data["position"][1]))
				$this->position = strval($_data["position"][0]) . "," . strval($_data["position"][1]); else
					return null;

			# mid, small, big
			if (isset($_data["size"]) && !is_null($_data["size"]))
			{
				if (in_array($_data["size"], array("mid", "small", "big")))
					$this->size = $_data["size"]; else
						$this->size = "mid";
			}

			if (isset($_data["label"]) && !is_null($_data["label"]))
			{
				if (is_numeric($_data["label"]) && $_data["label"] > 0 && $_data["label"] < 100)
					$this->label = $_data["label"]; else
				if (is_string($_data["label"]))
					$this->label = $_data["label"][0];
			}

			# red, lavender, lightblue, darkblue, green, grey, orange, white
			if (isset($_data["color"]) && !is_null($_data["color"]))
			{
				if (in_array($_data["color"], array("red", "lavender", "lightblue", "darkblue", "green", "grey", "orange", "white")))
					$this->color = $_data["color"]; else
						$this->color = "red";
			}

			if (isset($_data["opacity"]) && !is_null($_data["opacity"]) && is_numeric($_data["opacity"]))
				$this->opacity = $_data["opacity"];

			if (isset($_data["image"]) && !is_null($_data["image"]) && is_string($_data["image"]))
				$this->image = $_data["image"];
		}

		/**
		 *  URL converter. An important method for this class, used internally by {@link StaticMapService} class to get right image.
		 *
		 *  @return string Returns URL-encoded {@link Marker} object's value.
		 */

		function toUrl()
		{
			$url = "marker=";

			if (!is_null($this->size))
				$url .= "size:" . strval($this->size) . "|";

			if (!is_null($this->label))
				$url .= "label:" . strval($this->label) . "|";

			if (!is_null($this->color))
				$url .= "color:" . strval($this->color) . "|";

			if (!is_null($this->opacity))
				$url .= "opacity:" . strval($this->opacity) . "|";

			if (!is_null($this->image))
				$url .= "url:" . strval($this->image) . "|";

			$url .= $this->position;

			return $url;
		}
	};

	/**
	 *  @see Path
	 *  @package Cloudmade
	 *
	 *  Path class. Represents paths in Cloudmade's Static Maps service.
	 *
	 *  @property int $style Style ID. Defaults to 1.
	 *  @property array $points {@link Point} objects array, defining line.
	 */

	class Path
	{
		var $style, $points;

		/**
		 *  Constructor.
		 *
		 *  @param array $_data Arguments hash. Possible values are:
		 *  <ul>
		 *    <li>
		 *      <b>weight</b>
		 *      Thickness of the path in pixels. Defaults to 4.
		 *    </li>
		 *    <li>
		 *      <b>color</b>
		 *      24-bit mask specifying the color or one the predefined colors. Examples: 0x6faaff, orange. Defaults to black.
		 *    </li>
		 *    <li>
		 *      <b>opacity</b>
		 *      Use this to adjust transparency level. 1 is for completely opaque, 0.0 is for completely transparent (invisible). Defaults to 0.5.
		 *    </li>
		 *    <li>
		 *      <b>fill</b>
		 *      Color of the polygon. See color for detail on usage.
		 *    </li>
		 *    <li>
		 *      <b>fill-opacity</b>
		 *      See opacity for details on usage. Defaults to 0.5.
		 *    </li>
		 *    <li>
		 *      <b>point</b>
		 *      Sets path on the map image. Possible values are: {@link Line} object, {@link Point} objects array or array of data, needed to create a path. You can pass array of arrays of data, if needed.
		 *    </li>
		 *  </ul>
		 * 
		 */
		function Path($_data)
		{
			if (is_null($_data) || !is_array($_data))
				return null;

			$color = $opacity = $weight = $fill = $fill_opacity = null;
			$this->points = "";

			foreach ($_data as $k => $v)
			{
				if ($k == "color" && is_numeric($v))
					$color = "color:" . strval($v) . "|"; else

				if ($k == "weight" && is_numeric($v))
					$weight = "weight:" . strval($v) . "|"; else

				if ($k == "opacity" && is_numeric($v))
					$opacity = "opacity:" . strval($v) . "|"; else

				if ($k == "fill" && is_numeric($v))
					$fill = "fill:" . strval($v) . "|"; else

				if ($k == "fill-opacity" && is_numeric($v))
					$fill_opacity = "fill-opacity:" . strval($v) . "|"; else

				if (strpos("point", $k) >= 0)
				{
					if (is_object($v))
					{
						$this->points .= $v->toUrl() . "|";
					} else
					if (is_array($v))
					{
						if (is_object(current($v)))
						{
							foreach ($v as $j)
								$this->points .= $j->toUrl() . "|";
						} else
						if (is_array(current($v)))
						{
							foreach ($v as $j)
							{
								$p = new Point($j);
								$this->points .= $p->toUrl() . "|";
							}
						}
					}
				}
			}

			$this->style = $color . $weight . $opacity . $fill . $fill_opacity;
		}

		/**
		 *  URL converter. Converts current object to a valid URL format.
		 *
		 *  @return string Valid URL-encoded string value of $this.
		 */

		function toUrl()
		{
			$url = "path=";

			if (!is_null($this->style))
				$url .= $this->style;

			if (!is_null($this->points))
				$url .= $this->points;

			return $url;
		}
	};

?>
