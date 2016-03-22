<?php

	chdir("../");
	include "utils.php";

	$file	= file_get_contents("data/SUBDATA.DAT/START.DAT/00000049.bin");
	$len	= strlen($file);


	for ($ofs = 0x0008; $ofs < $len; $ofs += 0x28) {

		$sjis	= substr($file, $ofs, 0x22);
		// 0x22 is unused
		$bonusrank	= \Disgaea\DataStruct::getLEValue(substr($file, $ofs + 0x23, 0x01));
		$mapid		= \Disgaea\DataStruct::getLEValue(substr($file, $ofs + 0x24, 0x02));
		$scriptid	= \Disgaea\DataStruct::getLEValue(substr($file, $ofs + 0x26, 0x02));
		printf("%08x: [%02x %04x %04x] %s\n", $ofs, $bonusrank, $mapid, $scriptid, sjis($sjis));
	}

	print "\n";
