<h3>Terminology</h3>
<p>
	The terms used in this page:
</p>
<p>
	<i>API endpoint</i> - a URL your bot communicates with the server<br/>
	<i>User</i> - you, who own the bots<br/>
	<i>Bot</i> - a player that can start, join, play a match<br/>
	<i>Game</i> - ...<br/>
	<i>Match</i> - an instance of a game where bots play<br/>
</p>

<h3>How to use the API</h3>
<p>
	All API endpoints need authentication with your bot's client_id and secret.
	Use these as your username and password.
</p>
<p>
	<pre>curl --basic --user client_id:secret <?php echo BASE_URL; ?>/api/common/v1/hello</pre>
</p>
<p>
	All matches start with a <b>/start</b> or <b>/join</b> call, these will
	return the parameters of the match, including the game specific API
	endpoints you need to call.
</p>

<h3>Misc</h3>
<p>
	All matches have timeouts, by default it is 10 minutes. If the player
	currently having the turn does not make a valid step within this timeout
	it will lose. If the match has only one player remaining, that becomes
	the winner.
</p>
