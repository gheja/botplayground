<?php
	require_once("config.php");
	
	session_start();
	
	if (array_key_exists("user_name", $_SESSION))
	{
		echo "Hello " . $_SESSION["user_full_name"] . "!<br/>";
		echo "<a href=\"logout\">Log out</a>";
		
		print_r($_SESSION);
	}
	else
	{
		echo "Welcome, please ";
		echo "<a href=\"https://github.com/login/oauth/authorize?scope=user:read&client_id=" . GITHUB_CLIENT_ID . "\">log in with GitHub</a>.";
	}
?>
