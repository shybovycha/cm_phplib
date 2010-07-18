<?php
	require_once "../lib/Cloudmade.php";

	class MockConnection extends Connection
	{
		var $url, $request, $return_data;

		function set_data($_val)
		{
			$this->return_data = $_val;
		}

		function connect($_url, $_request)
		{
			$this->url = $_url;
			$this->request = $_request;

			return $this->return_data;
		}
	}
?>
