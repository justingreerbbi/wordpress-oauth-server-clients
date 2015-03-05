	<?php
	/**
	 * OAuth_Client.php
	 *
	 * OAuth Client Class. This is bare bones as of now and will be updated
	 * as time goes on.
	 *
	 * @author Justin Greer <justin@justin-greer.com>
	 * @version 1.2.0
	 * @copyright 2013 Justin Greer Interactive, LLC
	 * @license GPL2
	 * 
	 * This program is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License, version 2, as 
	 * published by the Free Software Foundation.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 * in your development cycle save you a lot of time by preventing you having to rewrite<br>
	 * major documentation parts to generate some usable form of documentation.
	 *
	 * client_id = c42eaad93bdfb69dff238031c7c94613
	 * clinet_scret = f67c2bcbfcfa30fccb36f72dca22a817
	 * user_id = 1
	 * redirect_uri = http://blackbirdi.com
	 *
	 * AUTHORIZE
	 * oauth/authorize/?client_id=c42eaad93bdfb69dff238031c7c94613&response_type=code
	 */
class OAuthClient 
{
		
	const VERSION = "1.0.0";
	const RESPONSE_CODE_PARAM = 'code';

	protected $_serverUrl = "http://serveruri.dev/";
  protected $_config = array();
	protected $_accessCode = null;
	protected $_accessToken = null;
	protected $_currentUser = null;
	protected $_httpClientResponse = null;

	/**
	 * Setup the OAuth object to be used
	 *
	 * @todo It may be quicker to simply set the config as the array instead of looping. Not really
	 * a big deal but may be a thought.
	 *
	 * @todo You could add a check to make sure that all the given params are given. This way you can 
	 * controll the errors.
	 */
	function __construct ( $config )
	{
		$this->_config = $config;
		$this->_endpointUrls = array(
	       'authorize' 	=> $this->config['_serverURL'] . 'oauth/authorize/?client_id=%s&redirect_uri=%s&response_type=%s&scope=likes+comments',
	       'token' 			=> $this->config['_serverURL'] . 'oauth/token?code=%s&grant_type=authorization_code&client_id=%s',
	   	);
	}

	/**
	 * Get the Access code provided during the authorization request
	 */
	protected function getAccessCode ()
	{
		return @$_GET[self::RESPONSE_CODE_PARAM];
	}

	/**
	 * Set the Access Token
	 * @param [type] $accessToken [description]
	 */
	public function setAccessToken ( $accessToken )
	{
		$this->$_accessToken = $accessToken;
	}

	/**
	 * Return a caonfig varaible from current object
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 * 
	 * @todo Do proper check for variable instead of surpressing any errors
	 */
	public function get_config ($key)
	{
		return @$this->_config[$key];
	}

	/**
	 * [_getAccessToken description]
	 * @return [type] [description]
	 */
	public function _getAccessToken ($params=null)
	{
		$uri = sprintf($this->_endpointUrls['token'],
			$this->getAccessCode(),
			$this->_config['client_id']
		);

		return $this->_initHttpClient($uri);
	}
		
	/**
	 * Initiate the HTTP Client
	 * @param  [type]  $uri    [description]
	 * @param  [type]  $method [description]
	 * @param  boolean $params [description]
	 * @return [type]          [description]
	 *
	 * @todo Add cURL since is more likely to be working since "allow_url_fopen" is known to be 
	 * turned false on shared hosting servers. Maybe even adding a simple option in WP admin to check which 
	 * functionality to use.
	 */
	public function _initHttpClient($uri, $method = "GET", $params =null)
	{
		return json_decode(file_get_contents($uri));
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$uri);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
	}
		
}