<?php
/*****************************************************

Copyright ? If you ever get rich, send me a ferrari @ jozsab1@gmail.com

*****************************************************/

include_once('class.SnakeHive.php');

$Snakes = new SnakeHive;
$Snakes->SetDirection( "Tudi69", 0 );

for( $i=0;$i<5;$i++)
{
	$Snakes->Update();
	sleep( 1 );
}

?>