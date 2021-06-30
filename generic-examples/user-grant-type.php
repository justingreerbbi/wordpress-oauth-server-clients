<?php
require_once( dirname( __FILE__ ) . '/config.php' );

$redirect_uri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$tmp = null;

/**
 * This section is demonstrating the basics of capturing the user login informaiton from a form and then posting the
 * info to WP OAuth Server to get an access token
 */
if ( isset( $_POST['action'] ) ) {

	$curl_post_data = array(
		'grant_type'    => 'password',
		'username'      => $_POST['username'],
		'password'      => $_POST['password'],
		'client_id'     => $client_id,
		'client_secret' => $client_secret
	);

	$curl = curl_init( $server_url . '/oauth/token' );
	//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	//curl_setopt( $curl, CURLOPT_USERPWD, $client_id . ':' . $client_secret );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_POST, true );
	curl_setopt( $curl, CURLOPT_POSTFIELDS, $curl_post_data );
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5' );
	curl_setopt( $curl, CURLOPT_REFERER, 'http://www.example.com/1' );

	$tmp = json_decode( curl_exec( $curl ) );
	curl_close( $curl );


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

    <title>Authorization Code Grant Type - Example</title>
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
    <form action="" method="post">
        <input type="hidden" name="action" value="usercredentials"/>
        <input type="text" name="username" placeholder="username"/><br/>
        <input type="password" name="password" placeholder="password"/><br/>

        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>"/>
        <input type="hidden" name="client_secret" value="<?php echo $client_secret; ?>"/>

        <button type="submit">Login</button>
    </form>
<?php } ?>

</body>
</html>
