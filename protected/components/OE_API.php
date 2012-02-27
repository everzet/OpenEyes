<?php
/**
* _____________________________________________________________________________
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
* _____________________________________________________________________________
* http://www.openeyes.org.uk			 info@openeyes.org.uk
* --
*/

/**
 * OE API client interface
 */
class OE_API {
	/* These are only used if running outside of the Yii framework */
	public $host = 'localhost';
	public $user = 'admin';
	public $apikey = '';
	public $debug = false;

	function __construct() {
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);

		if (class_exists('Yii')) {
			$this->host = Yii::app()->params['apihost'];
			$this->user = Yii::app()->params['apiuser'];
			$this->apikey = Yii::app()->params['apikey'];
			$this->debug = Yii::app()->params['apidebug'];
		}
	}

	function get($url,$referer=false) {
		if ($this->debug) {
			echo "GET: $url\n";
		}

		$url = str_replace(' ','%20',$url);

		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_POST, false);
		if ($referer) {
			curl_setopt($this->curl, CURLOPT_REFERER, $referer);
		} else {
			curl_setopt($this->curl, CURLOPT_REFERER, null);
		}
		return curl_exec($this->curl);
	}

	function post($url, $post, $referer=false) {
		if ($this->debug) {
			echo "POST: $url\n";
		}

		$url = str_replace(' ','%20',$url);

		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_POST, true);
		if ($referer) {
			curl_setopt($this->curl, CURLOPT_REFERER, $referer);
		} else {
			curl_setopt($this->curl, CURLOPT_REFERER, null);
		}
		if (is_string($post)) {
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
		} else {
			$postfields = '';
			foreach ($post as $key => $value) {
				if ($postfields) $postfields .= '&';
				$postfields .= "$key=".rawurlencode($value);
			}
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postfields);
		}
		return curl_exec($this->curl);
	}

	function call($uri, $post=false) {
		$uri = preg_replace('/^\//','',$uri);
		$url = 'http://'.$this->host.'/api/'.$uri;

		if (preg_match('/\?/',$url)) {
			$url .= '&';
		} else {
			$url .= '?';
		}
		
		$url .= 'apiuser='.$this->user.'&apikey='.$this->apikey;

		if ($post) {
			$resp = $this->post($url, $post);
		} else {
			$resp = $this->get($url);
		}

		if (!$data = json_decode($resp,true)) {
			die("Invalid response (not JSON): $resp\n");
		}

		return $data;
	}
}
?>
