<?php
	require_once("config.php");
	
	session_start();
	
	if (array_key_exists("user_name", $_SESSION))
	{
		// already logged in, redirect to index
		
		header("Location: /");
		die();
	}
	
	if (!array_key_exists("code", $_GET))
	{
		echo "Bad request.";
		die();
	}
	
	if (!preg_match('/^[0-9a-f]+$/', $_GET["code"]))
	{
		echo "Bad request.";
		die();
	}
	
	$code = $_GET["code"];
	
	// get the GitHub access token based on the supplied code
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://github.com/login/oauth/access_token");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id=" . GITHUB_CLIENT_ID . "&client_secret=" . GITHUB_CLIENT_SECRET . "&code=" . $code);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept" => "application/json"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$a = curl_exec($ch);
	
	curl_close($ch);
	
	// check if we have the access token
	if (!preg_match('/access_token=([0-9a-f]+)/', $a, $b))
	{
		echo "Authentication error.";
		die();
	}
	
	$token = $b[1];
	
	// get the details of the user from GitHub using the access token
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.github.com/user");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept" => "application/json"));
	curl_setopt($ch, CURLOPT_USERAGENT, GITHUB_USER_NAME);
	curl_setopt($ch, CURLOPT_USERPWD, GITHUB_USER_NAME . ":" . $token);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$a = curl_exec($ch);
	
	curl_close($ch);
	
	$b = @json_decode($a);
	
	// check if the response was a JSON
	if (!$b)
	{
		echo "Authentication error.";
		die();
	}
	
	// store the details of the user
	// note: we are not storing the access token so we can no longer access
	//       the user's data, this is intentional
	
	$_SESSION["user_full_name"] = $b->name;
	$_SESSION["user_name"] = $b->login;
	$_SESSION["user_id"] = $b->id;
	
	header("Location: /");
?>
