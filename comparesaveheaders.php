<?php

	if (!isset($argv[1]) || !isset($argv[2])) {
		die("Usage: <original-SAVExxx.DAT> <new-decompressed.bin>\nReplaces compressed data in original save file with given decompressed data\n");
	} elseif (!file_exists($argv[1]) || !file_exists($argv[2])) {
		die("Filename $argv[1] or $argv[2] not found\n");
	}

	require_once("utils.php");

	print "Loading save data\n";
	$save		= new \Disgaea\SaveFile($argv[1]);
	$save2		= new \Disgaea\SaveFile($argv[2]);

	dumpSaveStuff($save, $save2);


	print "\n";

