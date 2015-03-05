<?php
/**
 * OAuth_Login.php
 *
 * The OAuth server will redirect to this file/url.
 * From here the script will communication between the client and OAuth server
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
 */
session_start();
require_once(dirname(__FILE__). '/config.php');
require_once( 'library/OAuthClient.php' );

/**
 * Check if there is an error present
 */
if(isset($_GET["error"]))
	die(@$_GET["error_description"]);

// Load the client class
$client = new OAuthClient($config);

/**
 * EXAMPLE - LISTENING FOR A AUTHORIZE CODE AND GETTING AN ACCESS TOKEN
 *
 * If there is a $_GET parameter "code", we must assume that the user has been authoricated and that
 * the OAuth Server is giving us an "Access Code" that we can use to abtain an access token.
 *
 * Once we have an Access Code present we can simply request an access_token.
 * - Access Codes are only valid for a maximum of 10 minutes. Please refer to the OAuth Server for it spcific speficatons.
 */
if( isset( $_GET['code'] ) ) {
	
	// Get the access token along with other information from the server as well 
	$feedback = $client->_getAccessToken();

	/** OPTION but RECOMMENDED - STORAGE */
	// Store the access token, refresh token as well as exiration from information gathered from the 
	// OAuth Server. Here the example simple adds the entire respose from the OAuth Server into a 
	// session. One could also save the this information to a database so it can be used later.
	// An access_token is only good as long as the OAuth Server specifies. 
	$_SESSION['USER'] = $feedback;

	// Once you have an access token, you know that user has signed in sucessfully and that they have
	// authorized your application (if scope is supported). Here is where you can call the resource server
	// and get user informaiton about the user. What you do with this information is up to you.
	// In this example, we are simply going to redirect back to the home page.
	//header("Location: /");	
}