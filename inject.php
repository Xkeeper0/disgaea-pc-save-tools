<?php

	if (!isset($argv[1]) || !isset($argv[2])) {
		die("Usage: <original-SAVExxx.DAT> <new-decompressed.bin>\nReplaces compressed data in original save file with given decompressed data\n");
	} elseif (!file_exists($argv[1]) || !file_exists($argv[2])) {
		die("Filename $argv[1] or $argv[2] not found\n");
	}

	require_once("utils.php");

	print "Loading save data\n";
	$save		= new \Disgaea\SaveFile($argv[1]);

	dumpSaveStuff($save);

	print "Loading new data\n";
	$newDecompressed	= file_get_contents($argv[2]);
	print "'Compressing' save data\n";
	$newCompressedData	= \Disgaea\CompressionHandler::compress($newDecompressed, 0x3F);

	print "Updating original save data\n";
	$save->setChunk("compressedData", $newCompressedData);
	$save->setChunk("compressedSize", strlen($newCompressedData) + 19);
	$save->setChunk("decompressedSize", strlen($newDecompressed));
	$save->setChunk("length", strlen($newCompressedData) + 19);

	$filename	= substr_replace($argv[1], ".new", -4, 0);
	print "Saving new save data to $filename\n";
	file_put_contents($filename, $save->getData());

	print "Done, maybe\n";

	dumpSaveStuff($save);


	print "\n";


	function dumpSaveStuff($s) {

		$read		= array(
			'unknown0'			=> "h",
			'xorkey'			=> "h",
			'unknown1'			=> "i",
			'unknown2'			=> "i",
			'unknown3'			=> "i",
			'length'			=> "i",
			'magic'				=> "h",
			'unknown4'			=> "i",
			'decompressedSize'	=> "i",
			'data'				=> "l",
			'compressedSize'	=> "i",
			'compressedData'	=> "l",
			);

		foreach ($read as $chunk => $type) {

			$d	= $s->getChunk($chunk);
			printf("%-20s: ", $chunk);
			if ($type == "h") {
				print bin2hex($d);
			} elseif ($type == "l") {
				print strlen($d) ." bytes (". sprintf("%6x", strlen($d)) .")";
			} else {
				print $d;
			}

			print "\n";
		}

	}