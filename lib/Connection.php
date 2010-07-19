<?php

	/**
	 *  @package Cloudmade
	 *  @copyright Cloudmade, 2010
	 *  @license LICENSE
	 */

	/**
	 *  Represents connection to Cloudmade's servers and handles queries and their results.
	 *
	 *  @see Connection
	 *  @package Cloudmade
	 */

    class Connection
    {
        var $base_url, $port, $api_key;

		/**
		 *  Constructor; initializes connection.
		 *
		 *  @param string $_api_keys Your API key to connect to CloudMade services
		 *  @param string $_base_url Should not start with 'www'
		 *  @param int $_port Integer value of port for CloudMade portal, if nil then default 80 port is used
		 */

        function Connection($_api_key = null, $_base_url = 'cloudmade.com', $_port = null)
		{
            if ($_base_url != '')
                $this->base_url = $_base_url; else
                    $this->base_url = 'cloudmade.com';

            $this->api_key = $_api_key;
            $this->port = $_port;
        }

		/**
		 *  Make a HTTP connection and send a request. Called by the cloudmade 'Client' object internally
		 *
		 *  @param string $server_url Url you want to use for request
		 *  @param string $request Your request
		 */

        function connect($server_url, $request)
        {
            $result = null;

			if (false)
				var_dump($request);

            $f = fopen($request, "r");

            if ($f) // && $errno <= 0 && $errstr == null)
            {
                while (!feof($f))
                    $result .= fread($f, 1);

				fclose($f);
            } else
            {
                die("Connection exception.");
            }

            return $result;
        }

		# Usage:
		# $content = PostRequest("http://www.example.com/", $data);
		# Not used yet.
		/*function postRequest($_url, $_data)
		{
			$referer = "cloudmade.com";

			// convert variables array to string:
			$data = array();

			while (list($n, $v) = each($_data))
			{
				$data[] = "$n=$v";
			}

			$data = implode('&', $data);

			// parse the given URL
			$_url = parse_url($_url);

			if ($_url['scheme'] != 'http')
			{
				die('Only HTTP request are supported !');
			}

			// extract host and path:
			$host = $_url['host'];
			$path = $_url['path'];

			// open a socket connection on port 80
			$fp = fsockopen($host, 80);

			// send the request headers:
			fputs($fp, "POST $path HTTP/1.1\r\n");
			fputs($fp, "Host: $host\r\n");
			fputs($fp, "Referer: $referer\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ". strlen($data) ."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $data);

			$result = "";

			while (!feof($fp))
			{
				// receive the results of the request
				$result .= fgets($fp, 128);
			}

			// close the socket connection:
			fclose($fp);

			// split the result header from the content
			$result = explode("\r\n\r\n", $result, 2);

			//$header = isset($result[0]) ? $result[0] : '';
			$content = isset($result[1]) ? $result[1] : '';

			// return as array:
			// return array($header, $content);
			return $content;
		}*/

        /**
		 *  Convenience method
		 *
		 *  @return string Return the base URL and port of this Connection
		 */

        function getUrl()
        {
			return $this->base_url . (($this->port != null) ? ':' . strval($this->port) : '');
        }

		/**
		 *  Getter for $baseUrl
		 *
		 *  @return string Returns $baseUrl value
		 */

		function getBaseUrl()
		{
			return $this->base_url;
		}

		/**
		 *  Getter for $apiKey
		 *
		 *  @return string Returns $apiKey value
		 */

		function getApiKey()
		{
			return $this->api_key;
		}

        /**
		 *  Getter for $port
		 *
		 *  @return string Returns $port value
		 */

        function getPort()
        {
            if ($this->port == null)
                return 80; else
                    return $this->port;
        }
    };

?>
