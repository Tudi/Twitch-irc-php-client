<?PHP
/*****************************************************

Copyright ? If you ever get rich, send me a ferrari @ jozsab1@gmail.com

*****************************************************/
set_time_limit( 5 * 60 * 60 ); //5 hours ?
//set_time_limit(0);
error_reporting(E_ALL);

if( isset( $nick ) == false )
{
	$host = "irc.twitch.tv";
	$port=6667;
	$nick="Tudi69"; // change to something unique. this aint gonna try twice.
	$ident="Tudi69";
	$chan="#sacriel";
	//get it from here : http://twitchapps.com/tmi/#access_token=f1zd53184iq4megdcf1ujhgrgylmvzk&scope=chat_login
	$auth="f1zd53184";
	$realname = "NoBodyCares";
	$PageTimeOut = 20;
}

foreach($_REQUEST as $request_name=>$request_value)
	$$request_name=mysql_escape_string($request_value);

?>

<table>
	<form name="get_data" action="<?=$_SERVER['PHP_SELF'];?>" method="post">
	<tr>
	  <td width="150" height="30" align="right">Twitch User:&nbsp;</td>
	  <td width="240"><input type="text" name="nick" style="width:180" value="<?=$nick?>"></td>
	  <td> Should be one made for the bot </td>
	</tr>
	<tr>
	  <td width="150" height="30" align="right">Channel:&nbsp;</td>
	  <td width="240"><input type="text" name="chan" style="width:180" value="<?=$chan?>"></td>
	  <td> Channel name the bot should monitor </td>
	</tr>
	<tr>
	  <td width="150" height="30" align="right">auth:&nbsp;</td>
	  <td width="240"><input type="text" name="auth" style="width:180" value="<?=$auth?>"></td>
	  <td> get auth code from here : http://twitchapps.com/tmi/ </td>
	</tr>
	<tr>
	  <td width="150" height="30" align="right">PageTimeOut:&nbsp;</td>
	  <td width="240"><input type="text" name="PageTimeOut" style="width:180" value="<?=$PageTimeOut?>"></td>
	  <td> time limit when bot will stop monitoring the channel </td>
	</tr>
	<tr><td><input type="hidden" name="HasValues" value="1"></td></tr>
	<tr><td><input type="submit" value="Start Bot"></td></tr>
	</form>
</table>

<?php

if( isset( $HasValues ) == false || $HasValues != 1 )
	exit();
	
$LineEnding = "\n\r<br>";

echo "including irc class...$LineEnding";
include_once('class.ircClient.php');

echo "initiating irc class and connecting...$LineEnding";
$ircbot = new ircClient($nick, $ident, $realname, $host, $port, $auth);

echo "joining channel..$LineEnding";
$ircbot->joinChan($chan); 

if( $PageTimeOut != 0 )
{
	echo "Setting page timeout to $PageTimeOut seconds..$LineEnding";
	$ircbot->Timout( $PageTimeOut ); 
}

//$ircbot->msgChan( "", "!greet" ); 

echo "entering loop..$LineEnding";
$ircbot->loop();

echo "disconnected. $LineEnding";

?> 