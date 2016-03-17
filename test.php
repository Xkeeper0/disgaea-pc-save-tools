<?php

	require_once("utils.php");

/*
	$test	= new \Disgaea\TestStruct("");
	var_dump($test->getAllChunks());
	print "\n\nSetting new values...\n";

	$test->setChunk("int", 0x1234);
	$test->setChunk("raw", "TEST");
	$test->setChunk("int2", 0xEFBEADDE);
	$test->setChunk("raw2", "oh");
	$test->setChunk("final", "this is the end of the data -->");

	print "Dumping new chunks:\n";
	var_dump($test->getAllChunks());

	print "Dumping new data:\n";
	var_dump(bin2hex($test->getData()));

	print "\n\n";
	die();


	$test	= \Disgaea\DataStruct::makeLEValue(0x12345678, 4);

	var_dump(bin2hex($test));





	die("\n\n");

*/

	$save		= new \Disgaea\SaveFile("saves/SAVE000.DAT");
	$saveData	= $save->getSaveObject();

	$characters			= $saveData->getChunk('characters');
	$items				= $characters[0]->getChunk("equipment");
	$items[0]->dump();
	$stats		= $items[0]->getChunk("stats");
	$stats->setChunk("hp", 42069);

	print "Updated HP stat, let's see what the data looks like now\n";

	$characters			= $saveData->getChunk('characters');
	$items				= $characters[0]->getChunk("equipment");
	$items[0]->dump();

	print "Updating save file...\n";
	$save->updateSaveFile($saveData);
	print "Writing save file...\n";
	$save->writeSaveFile("saves/TEST.DAT");

	print "Done\n";

	/*
	foreach ($characters as $id => $character) {
		printf("%3d: %s\n", $id, $character);
	}
	*/

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

