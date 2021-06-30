<?php
require_once( dirname( __FILE__ ) . '/config.php' );

$redirect_uri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Authorization Code Implicit Grant Type - Example</title>
    <meta name="description" content="Authorization Code Implicit Grant Type - Example">
    <meta name="author" content="WP OAuth Server">
    <link rel="stylesheet" href="assets/styles.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</head>

<body>

<h1>Authorization Code Implicit Grant Type Example</h1>
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
        <input type="hidden" name="response_type" value="token"/>
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>"/>
        <input type="hidden" name="redirect_uri" value="<?php echo $redirect_uri; ?>"/>
        <button type="submit">Log In using Single Sign On</button>
    </form>
<?php } ?>

<div id="response-code"></div>

<script>

    var settings = {
        server_url: 'https://wordpress.local',
        client_id: 'mvJsGPYZNHgRVSeoNQfrT4FN6wpunvVJ0FbHu9Hi',
        client_secret: 'RX9NQncDdr47wKUCgk6EQ2nRHyycIfuAdDo31AGf',
        redirect_uri: window.location.href,
        user_endpoint: 'https://wordpress.local/oauth/me'
    }

    // Parses the URL parameters and returns an object
    function parseParms(str) {
        var pieces = str.split("&"), data = {}, i, parts;
        // process each query pair
        for (i = 0; i < pieces.length; i++) {
            parts = pieces[i].split("=");
            if (parts.length < 2) {
                parts.push("");
            }
            data[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1]);
        }
        return data;
    }

    // Returns the token from the URL hash
    function getParam(param) {
        var hash = parseParms(document.location.hash.substring(1));
        return hash[param];
    }

    // Send the user to the authorize endpoint for login and authorization
    function authorize() {
        window.location = settings.server_url + "/authorize?response_type=token&scope=basic&client_id=" + settings.client_id + "&redirect_uri=" + settings.callback_url;
    }

    // Make a call using our token to the Echo API
    function getUserInfo(token, _cb) {
        $.ajax({
            url: settings.user_endpoint,
            method: "GET",
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function (response) {
                _cb(JSON.stringify(response, null, 2));
                $("#response-code").html(JSON.stringify(response, null, 2));
            }
        });
    }

    $(document).ready(function () {
        var access_token = getParam('access_token');
        if (access_token != undefined) {
            getUserInfo(access_token, function (user_info) {
                var user = $.parseJSON(user_info);
                console.log(user);
            });
        }

    });
</script>
</body>
</html>
