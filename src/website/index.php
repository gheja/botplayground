<?php
	require_once("config.php");
	
	session_start();
	
	if (!array_key_exists("user_name", $_SESSION))
	{
		include("template.header.php");
		echo "Welcome, please ";
		echo "<a href=\"https://github.com/login/oauth/authorize?scope=user:read&client_id=" . GITHUB_CLIENT_ID . "\">log in with GitHub</a>.";
		include("template.howto.php");
		include("template.footer.php");
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
	
	
	if (array_key_exists("action", $_GET))
	{
		switch ($_GET["action"])
		{
			case "new_bot":
				// if user has too many bots, don't allow new ones
				if (count($bots) >= BOT_COUNT_LIMIT_PER_USER)
				{
					echo "Too many bots registered. Delete one before creating a new.";
					die();
				}
				
				// validate "game_id" parameter
				if (!array_key_exists("game_id", $_GET))
				{
					echo "Invalid game_id.";
					die();
				}
				
				$game_id = $_GET["game_id"];
				
				$found = false;
				
				foreach ($games as $game)
				{
					if ($game["game_id"] == $game_id)
					{
						$found = true;
						break;
					}
				}
				
				if (!$found)
				{
					echo "Invalid game_id.";
					die();
				}
				
				// generate some random ids for the bot parameters
				$bot_client_id = bin2hex(random_bytes(16));
				$bot_secret = bin2hex(random_bytes(16));
				
				// insert it into the database
				$statement = $db->prepare("INSERT INTO bot (github_user_id, bot_client_id, bot_secret, game_id) values (?, ?, ?, ?)");
				$statement->bindValue(1, $_SESSION["user_id"]);
				$statement->bindValue(2, $bot_client_id);
				$statement->bindValue(3, $bot_secret);
				$statement->bindValue(4, $game_id);
				$statement->execute();
				
				// TODO: check for failed insert
				
				// go back to index
				// TODO: give some feedback
				header("Location: /");
				die();
			break;
			
			case "delete_bot":
				// validate "bot_client_id" parameter
				if (!array_key_exists("bot_client_id", $_GET))
				{
					echo "Invalid bot_client_id.";
					die();
				}
				
				$bot_client_id = $_GET["bot_client_id"];
				
				$found = false;
				$bot_id = null;
				
				// check if this bot really exists and owned by the user
				foreach ($bots as $bot)
				{
					if ($bot["bot_client_id"] == $bot_client_id)
					{
						$found = true;
						$bot_id = $bot["bot_id"];
						break;
					}
				}
				
				if (!$found)
				{
					echo "Invalid bot_client_id.";
					die();
				}
				
				// don't delete really just mark it as deleted
				// $statement = $db->prepare("DELETE FROM bot WHERE bot_id = ?");
				$statement = $db->prepare("UPDATE bot SET is_deleted = 1 WHERE bot_id = ?");
				$statement->bindValue(1, $bot_id);
				$statement->execute();
				
				// TODO: check for failed update
				
				// go back to index
				// TODO: give some feedback
				header("Location: /");
				die();
			break;
			
			default:
				echo "Invalid action.";
				die();
			break;
		}
	}
	
	include("template.header.php");
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
		echo "<h4>Base APIs:</h4>";
		echo "<ul>";
		echo "<li>Start a game: /api/common/v1/game/" . $game["game_name"] . "/start</li>";
		if ($game["game_players"] > 1)
		{
			echo "<li>Get a random running game: /api/common/v1/game/" . $game["game_name"] . "/join</li>";
		}
		echo "</ul>";
		echo "<h4>Your bots</h4>";
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
	
	include("template.howto.php");
	include("template.footer.php");
?>
