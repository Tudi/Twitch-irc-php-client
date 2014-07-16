<?php
/*****************************************************

Copyright ? If you ever get rich, send me a ferrari @ jozsab1@gmail.com

*****************************************************/

include_once('class.Snake.php');

$SnakeBoardPixelWidth = 1280;
$SnakeBoardPixelHeight = 800;
$SnakeFontPixelSize = 16;
$SnakeFont = 5;

class SnakeHive
{
	private $Snakes, $LastUpdateStamp;
	
	function SnakeHive()
	{
		$this->LastUpdateStamp = time();
	}
	
	function SetDirection( $pName, $NewDirection )
	{
		global $SnakeBoardPixelWidth, $SnakeBoardPixelHeight, $SnakeFontPixelSize;
		
		if( isset( $this->Snakes[ $pName ] ) == false )
		{
			$this->Snakes[ $pName ] = new Snake( $pName, $SnakeBoardPixelWidth / $SnakeFontPixelSize , $SnakeBoardPixelHeight / $SnakeFontPixelSize ); 
//echo "DEBUG:Creating new snake with name $pName <br>";
		}
		else
		{
//echo "DEBUG:Updating existing snake direction with name $pName <br>";
		}
		
		$this->Snakes[ $pName ]->SnakeSetDirection( $NewDirection );
	}
	
	function DisableSnake( )
	{
	}
	
	function Update()
	{
		global $SnakeBoardPixelWidth, $SnakeBoardPixelHeight, $SnakeFontPixelSize;
//echo "DEBUG:Updating snakes <br>";		
		if( count( $this->Snakes ) == 0 )
		{
//echo "DEBUG:No snakes to update <br>";		
			return;
		}
			
		if( time() - $this->LastUpdateStamp <= 0 )
		{
//echo "DEBUG:Not yet time to update ".(time() - $this->LastUpdateStamp)." <br>";		
			return;
		}
		
		$this->LastUpdateStamp = time();
			
		//create an image
		$im = imagecreatetruecolor( $SnakeBoardPixelWidth, $SnakeBoardPixelHeight );
		//set white background
		$BackGroundColor = imagecolorallocate( $im, 255, 255, 255 );
		imagefilledrectangle( $im, 0, 0, $SnakeBoardPixelWidth, $SnakeBoardPixelHeight, $BackGroundColor );
			
		foreach( $this->Snakes as $Name => $Snake )
		{
			$Snake->SnakeIncreaseScore( );
			
			//check if it Colide
			foreach( $this->Snakes as $Name2 => $Snake2 )
				if( $Snake->HasBittenOtherSnake( $Snake2 ) )
				{
					$Snake->SnakeResetScore( );
//echo "DEBUG:Snake $Name colided with $Name2 <br>";		
					break;
				}
			
			//draw snake
			$Snake->DrawSnake( $im );
			
			//make the snakes move
			$Snake->SnakeAdvance();
		}
		
		imagejpeg($im, "SnakeOut.jpg", 100 );

		// Free up memory
		imagedestroy($im);
		
	}
};