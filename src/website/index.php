<?php
	require_once("config.php");
	
	session_start();
	
	if (!array_key_exists("user_name", $_SESSION))
	{
		echo "Welcome, please ";
		echo "<a href=\"https://github.com/login/oauth/authorize?scope=user:read&client_id=" . GITHUB_CLIENT_ID . "\">log in with GitHub</a>.";
		die();
	}
	
	$db = new PDO("mysql:host=" . DB_HOSTNAME . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
	
	if (!$db)
	{
		echo "Cannot reach database.";
		die();
	}
	
	// fetch all games
	$statement = $db->prepare("SELECT * FROM game WHERE is_deleted != 1");
	$statement->execute();
	
	$games = $statement->fetchAll(PDO::FETCH_ASSOC);
	
	
	// fetch all bots owned by the user
	$statement = $db->prepare("SELECT * FROM bot WHERE github_user_id = ? AND is_deleted != 1");
	$statement->bindValue(1, $_SESSION["user_id"]);
	$statement->execute();
	
	$bots = $statement->fetchAll(PDO::FETCH_ASSOC);
	
	
	echo "Hello " . $_SESSION["user_full_name"] . "!<br/>";
	echo "<br/>";
	echo "You have <b>" . count($bots) . " bots</b> registered in total out of " . BOT_COUNT_LIMIT_PER_USER . ".<br/>";
	echo "<br/>";
	echo "<a href=\"logout\">Log out</a><br/>";
	
	echo "<h2>Games</h2>";
	
	// display all games and bots registered for those games
	foreach ($games as $game)
	{
		echo "<h3>" . $game["game_title"] . "</h3>";
		echo "<p>" . $game["game_description"] . "</p>";
		echo "<ul>";
		foreach ($bots as $bot)
		{
			if ($bot["game_id"] != $game["game_id"])
			{
				continue;
			}
			
			echo "<li>";
			echo "Client ID: " . $bot["bot_client_id"] . "<br/>";
			echo "Secret: " . $bot["bot_secret"] . "<br/>";
			echo "Current match ID: (none)<br/>";
			echo "<a href=\"?action=delete_bot&bot_client_id=" . $bot["bot_client_id"] . "\" onclick=\"javascript: return confirm('Are you sure you want to delete this bot?');\">Delete</a>";
			echo "</li>";
		}
		echo "<li><a href=\"?action=new_bot&game_id=" . $game["game_id"] . "\">Add new</a></li>";
		echo "</ul>";
	}
?>
