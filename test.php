<?php

	require_once("utils.php");

	$decompressedData	= file_get_contents("saves/SAVE000.DAT.bin");

	var_dump(\Disgaea\Data\Stats::$size);

	// Test item data
	$item	= new \Disgaea\Data\Item(substr($decompressedData, 0x0BC0, 0x90));

	$itemData	= $item->getAllChunks();

	var_dump($itemData);

	print "\n\n";

	print json_encode($itemData);

	print "\n";