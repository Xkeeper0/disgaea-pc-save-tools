<?php

	require_once("utils.php");

	$decompressedData	= file_get_contents("saves/SAVE003.DAT.bin");

	$saveObject			= new \Disgaea\SaveData($decompressedData);

	$characters			= $saveObject->getChunk('characters');

	foreach ($characters as $id => $character) {
		printf("%3d: %s\n", $id, $character);
	}

	die("\n\n");
	/*
	$chara	= new \Disgaea\Data\Character(substr($decompressedData, 0x0BB8, 0x6B8));
	$map	= $chara->getMap();
	generateHTMLMap($map);
	die();
	*/
	/*
	// Test item data
	$item	= new \Disgaea\Data\Item(substr($decompressedData, 0x0BC0, 0x90));

	$itemData	= $item->getAllChunks();

	var_dump($itemData);
	*/

	// Test character data

	/*
	$chara	= new \Disgaea\Data\Character(substr($decompressedData, 0x0BB8, 0x768));
	$map	= $chara->getMap();


	$charaData	= $chara->getAllChunks();
	var_dump($charaData);
	print "\n\n";
	print json_encode($charaData);
	*/


	for ($i = 0; $i < 256; $i++) {

		$o	= 0x0BB8 + 0x6B8 * $i;
		$chara	= new \Disgaea\Data\Character(substr($decompressedData, $o, 0x6B8));

		printf("Chara %3d (%05X): %s / %s\n", $i, $o, $chara->getChunk("name"), $chara->getChunk("title"));

	}

	//var_dump();
	/*
	$charaData	= $chara->getAllChunks();


	print "\n";

	var_dump($chara->getChunk("name"));
	print "\n";
	*/

	print "\n\n";

