<?php

	/**
	 *  @copyright Cloudmade, 2010
	 *  @license license.txt
	 */

	require_once "Cloudmade.php";

	/**
	 *  @see Service
	 *  @package Cloudmade
	 *
	 *  Base class for all services in this API.
	 *
	 *  @property string $subdomain Service's subdomain.
	 *  @property Connection $connection Connection object.
	 */

	class Service
	{
		var $subdomain, $connection;

		/**
		 *  Constructor. Makes Service class abstract (ideologically).
		 *
		 *  @param Connection $_connection Existing {@link Connection} object.
		 *  @param string|null $_subdomain Service's subdomain. Could be null.
		 */

		protected function Service($_connection, $_subdomain = null)
		{
			$this->connection = $_connection;
			$this->subdomain = $_subdomain;
		}

		/**
		 *  Service-special method, allowing connect to the server and get response to a given request.
		 *
		 *  @param string $_request Query
		 *  @return string Server's response
		 */

		protected function connect($_request)
		{
			if ($this->connection != null)
				return $this->connection->connect($this->getUrl(), $this->urlTemplate() . $_request); else
					return null;
		}

		/**
		 *  Getter.
		 *
		 * @return string Returns current service's URL.
		 */

		function getUrl()
		{
			return $this->subdomain . "." . $this->connection->getBaseUrl();
		}

		/**
		 *  Forms the URL for request op. For internal use only.
		 */

		function urlTemplate()
		{
			return "http://" . $this->subdomain . "." . $this->connection->getUrl() . "/" . $this->connection->getApiKey();
		}

		/**
		 *  Converts given arguments to URL.
		 *
		 *  @param array $_params Hash, giving data, you want convert to string.
		 *  @return string String value for argument given.
		 */

		function toUrlParams($_params)
		{
			$s = "";

			if (!is_array($_params) || $_params == null)
				return null;

			foreach ($_params as $k => $v)
				$s .= rawurlencode($k) . "=" . rawurlencode($v) . "&";

			$s = trim($s, "&");

			return $s;
		}
	};

?>
