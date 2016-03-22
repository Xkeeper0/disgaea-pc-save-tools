<?php

	chdir("../");
	include "utils.php";

	$file	= file_get_contents("data/SUBDATA.DAT/START.DAT/00000000.bin");
	$len	= strlen($file);

	/*
	Header
	u32 count;
	u32 offsets[count];
	u32 ids[count];

	where e.g. offsets[0] is the offset to the first script entry in the file (counted from after the ids array ends) and ids[0] is the script ID for the same script


	<FireFly> *most* of the commands are  u8 op; u8 nargs; u8 args[nargs];
	<FireFly> which is very generous that they give the size of the command upfront
	<FireFly> Now of course the ones that *don't* follow that pattern are variable-length and really annoying to try to find a pattern among


	*/


	$count		= \Disgaea\DataStruct::getLEValue(substr($file, 0x00000000, 0x00000004));
	$offsetofs	= 0x00000004;						// First after "count"
	$idsofs		= 0x00000004 * ($count + 1);		// After offset array
	$dataofs	= 0x00000004 * ($count * 2 + 1);	// After ids array

	$ids		= array();
	$offsets	= array();
	$idmap		= array();

	for ($i = 0; $i < $count; $i++) {
		$offsets[$i]		= \Disgaea\DataStruct::getLEValue(substr($file, $offsetofs + 4 * $i, 0x00000004));
		$ids[$i]			= \Disgaea\DataStruct::getLEValue(substr($file, $idsofs + 4 * $i, 0x00000004));
		
		if (isset($idmap[$ids[$i]])) {
			print("Duplicate script ID encountered: ". $idmap[$ids[$i]] ."\n");
		}

		$idmap[$ids[$i]]	= $i;
	}

	$data		= substr($file, $dataofs);

	for ($i = 0; $i < $count; $i++) {
		printf("%04x: ofs %08x   id %08x   ", $i, $offsets[$i], $ids[$i]);

		$len		= isset($offsets[$i + 1]) ? ($offsets[$i + 1] - $offsets[$i]) : strlen($data);

		$scriptdata	= substr($data, $offsets[$i], $len);

		file_put_contents("stuff/scripts/". sprintf("%08x", $ids[$i]) .".bin", $scriptdata);

		print "\n";


	}

	print "\n";



