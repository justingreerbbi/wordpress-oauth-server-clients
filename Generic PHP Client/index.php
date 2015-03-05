<?php require_once('config.php') ; ?>

<!-- 
	If the user is not logged into your site you would show the user this simple form. 
	You are also able to auto submit this form on load which would make this process smoother. 
-->
<form action="http://oauthserver.com/oauth/authorize/response_type=code&client_id=<?php echo $config['client_id']; ?>&redirect_uri=https://client-ul.com/callback.php" method="POST">
    <input type="hidden" name="some_value" value="test"/>
    <input type="submit" value="Post" />
</form>