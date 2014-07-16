<?
error_reporting(E_ALL);

/*****************************************************

Copyright ? If you ever get rich, send me a ferrari @ jozsab1@gmail.com

*****************************************************/


include_once('class.SnakeHive.php');

class ircClient
{
	private $fp, $readbuffer, $line, $mcommands;
	public $nick, $ident, $realname, $host, $port, $auth, $CurChan;
	private $Snakes;
	private $StartStamp;
	private $EndStamp;
	
	function ircClient($nick, $ident, $realname, $host, $port, $auth )
	{
		global $LineEnding;
		
		$this->nick 		= $nick;
		$this->ident 		= $ident;
		$this->realname 	= $realname;
		$this->host 		= $host;
		$this->port 		= $port;
		$this->auth 		= $auth;

		$this->fp = fsockopen($host, $port, $erno, $errstr, 30);
		if(!$this->fp) 
			die("Could not connect$LineEnding");
		
		fwrite($this->fp, "PASS oauth:".$auth."\n");
		fwrite($this->fp, "NICK ".$nick."\n");
//		fwrite($this->fp, "USER ".$ident." ".$host." bla :".$realname."\n");

		$this->flush();

		$this->Snakes = new SnakeHive;
		$this->StartStamp = time();
		$this->EndStamp = time();
//		$this->Snakes->SetDirection( "Tudi69", 0 );
	}
	
	function Timout( $Seconds )
	{
		$this->EndStamp = $this->StartStamp + $Seconds;
	}
	
	function loop()
	{
		global $LineEnding;
		// now for program loop //
		while (!feof($this->fp)) 
		{
			$this->line = fgets($this->fp, 256); // wait for a message

			if($this->is_ping($this->line)) 
				$this->pong();
						
			echo "read from irc : $this->line $LineEnding";
			
			if(strstr($this->line,"PRIVMSG"))
			{
				echo "PRIVMSG...  $LineEnding";
				// incoming private message //
				$msg = $this->msgToArray($this->line);
								
				// is this a command?
				if($command = $this->get_command($msg['msg']))
				{
					echo "processing command ($command)... $LineEnding";
					// erase command from message array  // array('from, 'chan', 'msg'); //
					$msg['msg'] = trim(str_replace($command,'',$msg['msg']));
					echo "parsing command ($command)... $LineEnding";
					$this->parse_command($command, $msg);
				}
			}
			
			$this->line = "";
			$this->flush();
			$this->wait(); // time to next cycle
			
			$this->Snakes->Update();
			
			if( $this->EndStamp != 0 && $this->EndStamp < time() )
				break;
		}

	}
	
	// outgoing //
	function out($msg) // raw message
	{
		if(@empty($msg)) 
			return false;
		if(!strstr($msg, "\n")) 
			$msg .= "\n";

		fwrite($this->fp, $msg);
		return true;
	}
	
	function setNick($nick)						
	{ 
		$this->out("NICK ".$nick."\n"); 
		$this->nick = $nick; 
	}
	function joinChan($channel) 			
	{ 
		$this->out("JOIN :".$channel."\n"); 
		$this->CurChan = $channel;
	}
	function quitChan($channel) 			
	{ 
		$this->out("PART :".$channel."\n"); 
	}

	function listChans() 							
	{ 
		$this->out("LIST\n"); 
	}
	function getTopic($channel)				
	{ 
		$this->out("TOPIC ".$channel."\n"); 
	}
	
	function msg($target, $msg) 			
	{ 
		$this->out("PRIVMSG $target :$msg\n"); 
	}
	function msgChan($channel, $msg) 	
	{ 
		if( $channel == "" )
			$this->msg($this->CurChan, $msg); 
		else
			$this->msg($channel, $msg); 
	}
	function msgUser($user, $msg) 		
	{ 
		$this->msg($user, $msg); 
	}
	
	function pong() 									
	{ 
		$this->out("PONG :".$this->host."\n"); 
	}
	function quit($msg="")						
	{ 
		$this->out("QUIT :$msg\n"); 
	}
	
	// incoming processing //
	function is_ping($line)						
	{ 
		if(strstr($line, 'PING')) 
			return true; 
	}
	function is_msg($line)						
	{ 
		if(strstr($line, 'PRIVMSG')) 
			return true; 
	}

	function msgToArray($line) // array('from, 'chan', 'msg');
	{
		$array = explode(":",$line);
				
		$from = explode("!",$array[1]);
		$from = trim($from[0]);
		
//		$fromchan = explode("#",$array[1]);
//		$fromchan = "#".trim($fromchan[1]);
		
		$string = $array[2];
		$string = trim($string);
		
//		$msg = array('from'=>$from, 'chan'=>$fromchan, 'msg'=>$string);
		$msg = array('from'=>$from, 'chan'=>$this->CurChan, 'msg'=>$string);
		
		return $msg;
	}
	
	// system
	function flush()									
	{ 
		@ob_flush; 
		@flush(); 
	}
	function wait()										
	{ 
		usleep(100000); 
	}
	function get_command($string)
	{
		if(!strstr($string,"!")) 
			return false;
		if(!strstr($string, " "))
			$command = $string;
		else
		{
			$command = explode(" ", $string,2);
			$command = $command[0];
		}
		return $command;
	}
	
	// misc useful functions //
	function rem_xs_whitespace($string)
	{ 
		$string = trim(preg_replace('/\s+/', ' ', 	$string)); 
		return $string; 
	}

	function parse_command($command, $msg)
	{
		global $LineEnding;
		// $command = "!command"; $msg = array('from, 'chan', 'msg')
		switch($command)
		{
			case '!greet'	: 
				$this->command_greet($msg); 
			case '!left'	: 
				$this->Snakes->SetDirection( $msg['from'], 0 );
			break;
			case '!up'	: 
				$this->Snakes->SetDirection( $msg['from'], 1 );
			break;
			case '!right'	: 
				$this->Snakes->SetDirection( $msg['from'], 2 );
			break;
			case '!down'	: 
				$this->Snakes->SetDirection( $msg['from'], 3 );
			break;
		}
		
/*		if( $msg['from'] != '' )
		{
//echo "DEBUG: Got a new message from ".$msg['from']."$LineEnding";	
			$this->Snakes->SetDirection( $msg['from'], rand( 0, 4 ) );
		} */
	}
	
	// now for commands //
	function command_greet($msg)
	{
		global $LineEnding;
		$number = rand(1,100);
		$this->msgChan($msg['chan'], "Hello allmighty Tudi the wonderboy of the frozen sands");
		echo "Testing greeting...$LineEnding";
	}
}

?>