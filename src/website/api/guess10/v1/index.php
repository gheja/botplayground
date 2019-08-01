<?php
	define("VERSION", "v1");
	define("BASE_URL", "/api/guess10/" . VERSION);
	
	require_once("../../../config.php");
	// define("BASE_PATH", 
	
	$url = substr($_SERVER["REQUEST_URI"], strlen(BASE_URL));
	
	function return_json($obj)
	{
		echo json_encode($obj);
		die();
	}
	
	function return_bad_request()
	{
		header("HTTP/1.1 400 Bad Request");
		return_json(array("result" => "error", "result_code" => 400, "result_text" => "Bad request"));
	}
	
	function return_unauthorized()
	{
		header("HTTP/1.1 401 Unauthorized");
		return_json(array("result" => "error", "result_code" => 401, "result_text" => "Unauthorized"));
	}
	
	function db_connect()
	{
		global $db;
		
		$db = new PDO("mysql:host=" . DB_HOSTNAME . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
		
		if (!$db)
		{
			echo "Cannot reach database.";
			die();
		}
	}
	
	function bot_auth()
	{
		global $db, $bot;
		
		// TODO: syntax check on parameters
		
		$statement = $db->prepare("SELECT * FROM bot WHERE bot_client_id = ? AND bot_secret = ? AND is_deleted != 1");
		$statement->bindValue(1, 123);
		$statement->bindValue(2, 123);
		$statement->execute();
		
		$bot = $statement->fetch(PDO::FETCH_ASSOC);
		
		print_r($bot);
		
		if (!$bot)
		{
			return_unauthorized();
		}
	}
	
	function match_auth()
	{
		global $db, $match;
		
		// TODO: syntax check on parameters
		
		if ($bot["current_match"] != "xxx")
		{
			return_unauthorized();
		}
		
		$statement = $db->prepare("SELECT * FROM match WHERE match_id = ? AND is_deleted != 1");
		$statement->bindValue(1, 123);
		$statement->execute();
		
		$match = $statement->fetch(PDO::FETCH_ASSOC);
		
		print_r($match);
		
		if (!$match)
		{
			return_unauthorized();
		}
	}
	
	$db = null;
	$bot = null;
	$match = null;
	
	switch ($url)
	{
		case "/start":
			db_connect();
			bot_auth();
		break;
		
		case "/join":
			return_bad_request();
		break;
		
		case "/guess":
			db_connect();
			bot_auth();
			match_auth();
		break;
	}
	
	return_bad_request();
?>
