<?php
/*****************************************************

Copyright ? If you ever get rich, send me a ferrari @ jozsab1@gmail.com

*****************************************************/

$SnakeLen = 20;
$SnakePartSize = 8;	//size of the font = resolution of our board
$SnakeDirectionLeft = 0;
$SnakeDirectionUp = 1;
$SnakeDirectionRight = 2;
$SnakeDirectionDown = 3;

class Snake
{
	public 	$Name, $Score;
	private $LastDirection, $PartHistoryX, $PartHistoryY;
	private $ColorR,$ColorG,$ColorB;
	private $IsActive;
	private $SurviveScore;
	private $MaxWidth, $MaxHeight;
	
	function Snake( $pName, $MaxX, $MaxY )
	{
		global $SnakeLen, $SnakePartSize, $SnakeDirectionLeft, $SnakeDirectionUp, $SnakeDirectionRight, $SnakeDirectionDown;
		
		$this->Name = $pName;
		$this->MaxWidth = $MaxX;
		$this->MaxHeight = $MaxY;
		$this->Activate();
	}
	
	function Activate()
	{
		global $SnakeLen, $SnakePartSize, $SnakeDirectionLeft, $SnakeDirectionUp, $SnakeDirectionRight, $SnakeDirectionDown;
		
		$this->PartHistoryX[ 0 ] = rand( 0, $this->MaxWidth ) * $SnakePartSize;
		$this->PartHistoryY[ 0 ] = rand( 0, $this->MaxHeight ) * $SnakePartSize;
		for( $i=1;$i<$SnakeLen;$i++)
		{
			$this->PartHistoryX[$i] = $this->PartHistoryX[ 0 ] + $i * $SnakePartSize;
			$this->PartHistoryY[$i] = $this->PartHistoryY[ 0 ];
		}
		$this->LastDirection = $SnakeDirectionLeft;
		$this->IsActive = 1;
		$this->SurviveScore = 1;
	}
	
	function SnakeResetScore( )
	{
		$this->SurviveScore = 1;
	}
	
	function SnakeIncreaseScore( )
	{
		$this->SurviveScore += 1;
	}
	
	function SnakeAdvance( )
	{
		global $SnakeLen, $SnakePartSize, $SnakeDirectionLeft, $SnakeDirectionUp, $SnakeDirectionRight, $SnakeDirectionDown;
		
		if( $this->IsActive == 0 )
			return;
			
//echo "DEBUG:Advancing snake $this->Name direction $this->LastDirection <br>";			
		//slide history
		for( $i = $SnakeLen - 1; $i > 0; $i-- )
		{
			$this->PartHistoryX[ $i ] = $this->PartHistoryX[ $i - 1 ];
			$this->PartHistoryY[ $i ] = $this->PartHistoryY[ $i - 1 ];
		}
		
		//calculate new Head position
		if( $this->LastDirection == $SnakeDirectionLeft )
			$this->PartHistoryX[ 0 ] -= $SnakePartSize;
		else if( $this->LastDirection == $SnakeDirectionUp )
			$this->PartHistoryY[ 0 ] -= $SnakePartSize;
		else if( $this->LastDirection == $SnakeDirectionRight )
			$this->PartHistoryX[ 0 ] += $SnakePartSize;
		else if( $this->LastDirection == $SnakeDirectionDown )
			$this->PartHistoryY[ 0 ] += $SnakePartSize;
	}

	function SnakeSetDirection( $NewDirection )
	{
		if( $this->IsActive == 0 )
			$this->Activate();
			
		$this->LastDirection = $NewDirection;
	}
	
	function HasBittenOtherSnake( $OtherSnake )
	{
		global $SnakeLen, $SnakePartSize, $SnakeDirectionLeft, $SnakeDirectionUp, $SnakeDirectionRight, $SnakeDirectionDown;
		if( $this->IsActive == 0 )
		{
			return 0;
		}
			
		if( $this->PartHistoryX[ 0 ] < 0 || $this->PartHistoryY[ 0 ] < 0 || $this->PartHistoryX[ 0 ] > $this->MaxWidth * $SnakePartSize || $this->PartHistoryY[ 0 ] > $this->MaxHeight * $SnakePartSize )
		{
//echo "DEBUG:Self Deactivating snake $this->Name <br>";		
			$this->IsActive = 0;
			return 1;
		}
		
		//snake head bite is not allowed
		for( $i = 1; $i < $SnakeLen; $i++ )
		{
			if( $this->PartHistoryY[ 0 ] == $OtherSnake->PartHistoryY[ $i ] 
				&& $this->PartHistoryX[ 0 ] == $OtherSnake->PartHistoryX[ $i ] )
			{
//echo "DEBUG:Colide at other snake index $i <br>";			
				return 1;
			}
		}
		return 0;
	}
	
	function SetColor( $R, $G, $B )
	{
		$this->ColorR = $R;
		$this->ColorG = $G;
		$this->ColorB = $B;
	}
	
	function GenerateText()
	{
		global $SnakeLen, $SnakePartSize, $SnakeDirectionLeft, $SnakeDirectionUp, $SnakeDirectionRight, $SnakeDirectionDown;
		$out = $this->Name;
		$out .= "@";
		$TempScore = $this->SurviveScore;
		$TextScore = "";
		for( $i = strlen( $out ) + 1; $i < $SnakeLen; $i++ )
		{
			$TextScore = ($TempScore % 10). $TextScore;
			$TempScore = (int)( (int)( $TempScore ) / 10 );
		}
		$out .= $TextScore;
		for( $i = strlen( $out ); $i < $SnakeLen; $i++ )
			$out .= "@";
		return $out;
	}
	
	function DrawSnake( $im )
	{
		global $SnakeFont, $SnakeLen, $SnakePartSize, $SnakeDirectionLeft, $SnakeDirectionUp, $SnakeDirectionRight, $SnakeDirectionDown;
		
		if( $this->IsActive == 0 )
			return;
		
		//textify the snake
		$text_color = imagecolorallocate( $im, $this->ColorR, $this->ColorG, $this->ColorB );	
		$SnakeText = $this->GenerateText();
		for( $i = 0; $i < $SnakeLen; $i++ )
		{
			$CurChar = $SnakeText[ $i ];
			if( $CurChar == '' )
				$CurChar = '@';
//echo "writing $CurChar at ".$this->PartHistoryX[ $i ]." ".$this->PartHistoryY[ $i ]."<br> ";			
			imagestring($im, $SnakeFont, $this->PartHistoryX[ $i ], $this->PartHistoryY[ $i ],  $CurChar, $text_color);
		}
	}
};

?>