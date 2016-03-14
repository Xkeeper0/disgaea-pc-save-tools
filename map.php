<?php

	require_once("utils.php");

	$decompressedData	= file_get_contents("saves/SAVE002.DAT.bin");
	
	$chara	= new \Disgaea\Data\Character(substr($decompressedData, 0x0BB8 + 0x6B8 * 5, 0x6B8));
	$map	= $chara->getMap();

	generateHTMLMap($map);
	die();
