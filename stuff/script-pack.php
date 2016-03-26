<?php

	chdir("../");
	include "utils.php";

	$count		= 0;	// # of scripts
	$offsets	= "";	// Binary: Offset list
	$ids		= "";	// Binary: ID list
	$scripts	= "";	// Binary: Concat'd scripts

	$s	= scandir("stuff/scripts/");
	$n	= count($s) - 3;

	foreach ($s as $f) {

		if ($f{0} !== "." && substr($f, -4) === ".bin") {

			// Add count
			$count++;

			// Add ID and Offset to those
			$id		= intval(substr($f, 0, -4));
			$offset	= strlen($scripts);

			printf("%4d/%4d: id %8d, ofs %08x\n", $count + 1, $n, $id, $offset);

			// Add offset list
			$offsets	.= \Disgaea\DataStruct::makeLEValue($offset, 4);
			$ids		.= \Disgaea\DataStruct::makeLEValue($id, 4);
			$scripts	.= file_get_contents("stuff/scripts/$f");

		}
	}

	$out	= \Disgaea\DataStruct::makeLEValue($count, 4)
			. $offsets
			. $ids
			. $scripts;

	file_put_contents("stuff/SCRIPT.DAT", $out);
