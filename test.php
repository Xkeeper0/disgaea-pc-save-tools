<?php

	if (!isset($argv[1])) {
		die("Missing savedata filename\n");
	} elseif (!file_exists($argv[1])) {
		die("Filename $argv[1] not found\n");
	}

	require_once("utils.php");

	#var_dump(\Disgaea\Data\ID::getInnocent(0x41));
	#die();


	$save		= new \Disgaea\SaveFile($argv[1]);
	$saveData	= new \Disgaea\SaveData($save->getRawSaveData());

	/*
	printf("Unknown val:       %6x\n", $save->getChunk("unknown4"));
	printf("Uncompressed size: %6x\n", $save->getChunk("decompressedSize"));
	printf("Compressed size:   %6x\n", $save->getChunk("compressedSize"));
	printf("Actual data len:   %6x\n", strlen($save->getChunk("data")));
	printf("Total data len:    %6x\n", strlen($save->getRawSaveData()));
	print "\n";
	*/

	$compressedData	= $save->getSaveObject();
	$compressedData->setLogLevel(1);

	try {
		$decompressedData	= $compressedData->decompress();
		file_put_contents("$argv[1].bin", $decompressedData);
		print "Wrote decompressed save file to $argv[1].bin\n";

	} catch (Exception $e) {
		print "Error decompressing save file: ". $e->getMessage() ."\n";
		file_put_contents("$argv[1].bin", $compressedData->getDecompressedData());
		print "Wrote probably-not-correct attempt at decompressing file to $argv[1].bin\n";
		die("\n");
	}

	// File created

	// Test item data
	$item	= new \Disgaea\Data\Item(substr($decompressedData, 0x0BC0, 0x90));
	$item->dump();


	print "\n";