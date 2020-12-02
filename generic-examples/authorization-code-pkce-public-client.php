<?php
/**
 * Below is constants that you should change to meet your needs
 */
$server_url    = 'https://wordpress.local';
$client_id     = 'mvJsGPYZNHgRVSeoNQfrT4FN6wpunvVJ0FbHu9Hi';
$client_secret = 'RX9NQncDdr47wKUCgk6EQ2nRHyycIfuAdDo31AGf';

$redirect_uri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$tmp = null;

/**
 * PCKE INFORMATION
 *
 * code_challenge_method = s256 | plain. Server should default to "plain" if not presented making this parameter optional
 */
function base64url_encode( $data ) {
	return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' );
}

function base64url_decode( $data ) {
	return base64_decode( str_pad( strtr( $data, '-_', '+/' ), strlen( $data ) % 4, '=', STR_PAD_RIGHT ) );
}

function get_code_verifier() {
	if ( empty( $_GET['code'] ) ) {
		unlink( dirname( __FILE__ ) . '/verifier.txt' );

		$str      = 'abcdefghijk';
		$shuffled = str_shuffle( $str );
		$f        = fopen( dirname( __FILE__ ) . '/verifier.txt', 'w' );
		fwrite( $f, $shuffled );
		fclose( $f );

		return $shuffled;
	} else {
		return file_get_contents( 'verifier.txt' );
	}
}

$code_verifier  = get_code_verifier();
$hash           = hash( 'sha256', $code_verifier, true );
$code_challenge = rtrim( strtr( base64_encode( $hash ), "+/", "-_" ), "=" );

/**
 * This section is demonstrating the basics of capturing the authorization code returned from WP OAuth Server.
 * It also shows how to use cURL to use the code to retrieve an access token.
 */
if ( isset( $_GET['code'] ) ) {

	$curl_post_data = array(
		'grant_type'    => 'authorization_code',
		'code'          => $_GET['code'],
		'redirect_uri'  => $redirect_uri,
		'client_id'     => $client_id,
		//'client_secret' => $client_secret,
		'code_verifier' => $code_verifier,
	);


	$curl = curl_init( $server_url . '/oauth/token/' );

	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_POST, true );
	curl_setopt( $curl, CURLOPT_POSTFIELDS, $curl_post_data );
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5' );
	curl_setopt( $curl, CURLOPT_REFERER, 'http://www.example.com/1' );

	$curl_response = curl_exec( $curl );
	$code_response = json_decode( $curl_response );
	curl_close( $curl );

	$tmp = $code_response;

	/*
	 * If there is no error in the return, the following will request the user information from the server
	 */
	$curl = curl_init( $server_url . '/oauth/me/?access_token=' . $tmp->access_token );

	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_POST, false );
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5' );
	curl_setopt( $curl, CURLOPT_REFERER, 'http://www.example.com/1' );

	$curl_response  = curl_exec( $curl );
	$token_response = json_decode( $curl_response );
	curl_close( $curl );

	$user = $token_response;
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Authorization Code Grant Type - PKCE Confidential Example</title>
	<meta name="description" content="Authorization Code Grant Type - Example">
	<meta name="author" content="WP OAuth Server">
	<link rel="stylesheet" href="assets/styles.css">
</head>

<body>

<h1>Authorization Grant Type Example</h1>
<p>Be sure to have changed the variables in the file <strong><?php echo __FILE__; ?></strong> before continuing.</p>
<hr/>

<?php if ( ! is_null( $tmp ) ) {
	print '<p>Below is the return from the OAuth Server. This information can be used to request the user information.</p>';
	print '<pre>';
	print_r( $tmp );
	print '</pre>';

	print '<p>Below is authorized user information given the access token provided. This information is what is used to log the user into the client.';
	print '<pre>';
	print_r( $user );
	print '</pre>';

	print '<a href="' . $redirect_uri . '">Return to Form</a>';
} else { ?>

	<h3>Login Form Example</h3>
	<p>The form below is a basic example of a simple button to present the user with a login button.</p>
	<form action="<?php echo $server_url; ?>/oauth/authorize/"
	      method="get">
		<input type="hidden" name="state" value="abcd123"/>
		<input type="hidden" name="scope" value="basic"/>
		<input type="hidden" name="response_type" value="code"/>
		<input type="hidden" name="client_id" value="<?php echo $client_id; ?>"/>
		<input type="hidden" name="redirect_uri" value="<?php echo $redirect_uri; ?>"/>
		<input type="hidden" name="code_challenge" value="<?php echo $code_challenge; ?>"/>
		<input type="hidden" name="code_challenge_method" value="s256"/>
		<button type="submit">Log In using Single Sign On</button>
	</form>
<?php } ?>

</body>
</html>
