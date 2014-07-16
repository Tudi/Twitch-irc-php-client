<?php
set_time_limit( 1 * 60 );

include_once('class.Snake.php');

$SnakeBoardPixelWidth = 1280;
$SnakeBoardPixelHeight = 800;
$SnakeFontPixelSize = 8;
	
//create some test snakes
$Snakes[0] = new Snake( "Tudi", $SnakeBoardPixelWidth / $SnakeFontPixelSize , $SnakeBoardPixelHeight / $SnakeFontPixelSize );
$Snakes[0]->SetColor( 0, 0, 255 );

$Snakes[1] = new Snake( "Pinky", $SnakeBoardPixelWidth / $SnakeFontPixelSize , $SnakeBoardPixelHeight / $SnakeFontPixelSize );
$Snakes[1]->SetColor( 255, 0, 0 );


for( $i=0; $i<30; $i++)
{
	//create an image
	$im = imagecreatetruecolor( $SnakeBoardPixelWidth, $SnakeBoardPixelHeight );
	//set white background
	$BackGroundColor = imagecolorallocate( $im, 255, 255, 255 );
	imagefilledrectangle( $im, 0, 0,$SnakeBoardPixelWidth ,$SnakeBoardPixelHeight , $BackGroundColor );
	
	//draw snakes
	$Snakes[0]->DrawSnake( $im );
	$Snakes[1]->DrawSnake( $im );
	
	//make the snakes move
	$Snakes[0]->SnakeAdvance();
	$Snakes[1]->SnakeAdvance();

	//test direction, make them do a square
	if( $i % 4 == 0 )
	{
//		echo "setting direction to ".( $i / 4 % 4)."<br>";	
		$Snakes[0]->SnakeSetDirection( $i / 4 % 4 );
		$Snakes[1]->SnakeSetDirection( $i / 4 % 4 );
	}
	// Save the image as 'simpletext.jpg'
//	imagejpeg($im, "SnakeTestOut_$i.jpg", 75 );
	imagejpeg($im, "SnakeTestOut.jpg", 75 );

	// Free up memory
	imagedestroy($im);

	sleep( 1 );
}

?>
