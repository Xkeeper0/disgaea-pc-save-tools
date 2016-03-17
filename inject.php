<?php

	if (!isset($argv[1]) || !isset($argv[2])) {
		die("Usage: <original-SAVExxx.DAT> <new-decompressed.bin>\nReplaces compressed data in original save file with given decompressed data\n");
	} elseif (!file_exists($argv[1]) || !file_exists($argv[2])) {
		die("Filename $argv[1] or $argv[2] not found\n");
	}

	require_once("utils.php");

	print "Loading save data\n";
	$save		= new \Disgaea\SaveFile($argv[1]);
	$save2		= new \Disgaea\SaveFile("saves/SAVE002.DAT");
	$oldSave	= clone $save;

	//dumpSaveStuff($save);

	print "Loading new data\n";
	$newDecompressed	= file_get_contents($argv[2]);

	print "'Compressing' save data\n";
	$newCompressedData	= \Disgaea\CompressionHandler::compress($newDecompressed);


	// override all that bullshit
	//$newDecompressed	= file_get_contents("saves/SAVE002.DAT.bin");
	//$newCompressedData	= $save2->getChunk("compressedData");

	print "Updating original save data\n";

	$compressedSize			= (strlen($newCompressedData) + 18);

	$save->setChunk("compressedData",	$newCompressedData);
	$save->setChunk("compressedSize",	$compressedSize);
	$save->setChunk("decompressedSize",	strlen($newDecompressed)       );
	$save->setChunk("length",			strlen($newCompressedData) + 19);
	$save->setChunk("lengthdiv4",		ceil($save->getChunk("length") / 4));

	$filename    = substr_replace($argv[1], ".new", -4, 0);
	print "Saving new save data to $filename\n";
	file_put_contents($filename, $save->getData());

	print "Done, maybe\n";

	print "save comparison --- | new file ---------------- | old file -----------------\n";
	dumpSaveStuff($save, $oldSave);


	print "\n";
