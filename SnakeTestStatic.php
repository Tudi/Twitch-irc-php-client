<?php

include_once('class.Snake.php');

$SnakeBoardPixelWidth = 1280;
$SnakeBoardPixelHeight = 800;
$SnakeFontPixelSize = 8;

$im = imagecreatetruecolor( $SnakeBoardPixelWidth, $SnakeBoardPixelHeight );

//set white background
$BackGroundColor = imagecolorallocate( $im, 255, 255, 255 );
imagefilledrectangle( $im, 0, 0,$SnakeBoardPixelWidth ,$SnakeBoardPixelHeight , $BackGroundColor );
		
//create some test snakes
$Snakes[0] = new Snake( "Tudi", $SnakeBoardPixelWidth / $SnakeFontPixelSize , $SnakeBoardPixelHeight / $SnakeFontPixelSize );
$Snakes[0]->SetColor( 0, 0, 255 );

$Snakes[1] = new Snake( "Pinky", $SnakeBoardPixelWidth / $SnakeFontPixelSize , $SnakeBoardPixelHeight / $SnakeFontPixelSize );
$Snakes[1]->SetColor( 255, 0, 0 );

$Snakes[0]->DrawSnake( $im );
$Snakes[1]->DrawSnake( $im );

// Save the image as 'simpletext.jpg'
imagejpeg($im, 'SnakeTestOut.jpg', 75 );

// Free up memory
imagedestroy($im);

?>
<meta http-equiv="refresh" content="1" >
<img src='SnakeTestOut.jpg'></img>