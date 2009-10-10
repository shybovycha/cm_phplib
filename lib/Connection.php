<?php

/**
 * Connection class to establish connection with Cloudmade's services server.
 * 
 * @package Connection
 * @author Artem Shybovych
 * 
 */

/**
 * Copyright 2009 CloudMade.
 *
 * Licensed under the GNU Lesser General Public License, Version 3.0;
 * You may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.gnu.org/licenses/lgpl-3.0.txt
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Connection to CloudMade's services.
 * API Client object is initialized by user credentials
 * (apikey/private key) as well as target (i.e. cloudmade)
 * host, port, etc.
 * 
 * @param string $apikey API key used for connection
 * @param string $host Host of CloudMade's services
 * @param int $port HTTP port to be used for connection
 */

class Connection {
    var $apikey, $host, $port;
    
    /**
     * Constructor
	 * 
	 * @param String $_apikey Your API key.
	 * @see http://developers.cloudmade.com/
	 * 
	 * @param String $_host Host. Should be "cloudmade.com" if you wanna use Cloudmade services.
	 * @param int $_port Port. As usual, it is 80.
     */
    
    function Connection($_apikey, $_host = "cloudmade.com", $_port = 80) {
        if (!isset($_apikey)) {
            echo new Exception('API key is not specified');
        } else {
            $this->apikey = $_apikey;
            $this->host = $_host;
            $this->port = $_port;
        }
    }

	/**
	 * Call CloudMade's service and return raw data
	 * 
	 * @return String - response of the service to the request
	 * 
	 * @param string $uri: Tail part of full-blown request URL.
	 * @tutorial '/api/action/thingy?get=True'
     * @param string $subdomain Subdomain, from which request should be
     * processed
	 */
    function call_service($uri, $subdomain = null) {
        $domain = $this->host;
        
        if (isset($subdomain))
            $domain = $subdomain . '.' . $this->host;
        
        if (isset($port))
            $domain = $domain . ':' . $this->port;

		$uri = '/' . $this->apikey . $uri;
		$uri = 'http://' . $domain . $uri;
		
		$request = new HttpRequest($uri, HttpRequest::METH_GET);
		
		try {
            $request->send();
            
            return $request->getResponseBody();
        } catch(Exception $e) {
            echo $e;
            
            return null;
        }
    }

	/**
	 * Connection object factory
	 *
     *  This function is subject to change. Currently it simply
     * constructs a connection. It will be expanded to a more functional
     * version later.
     * 
     * @param string $apikey API key used for connection
     * @param string $host Host of CloudMade's services
     * @param int $port HTTP port to be used for connection
     * 
     * @return Constructed connection object
	 */
    function get_connection($_apikey, $_host = "cloudmade.com", $_port = 80) {
        return new Connection($_apikey, $_host, $_port);
    }
}
?>
