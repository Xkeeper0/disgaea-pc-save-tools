#!/usr/bin/php
<?php


	$ic	= "";

	$args	= array_slice($argv, 1);

	foreach($args as $c) {
		$ic	.= chr(hexdec($c));
	}


	var_dump(iconv("shift-jis", "utf8", trim($ic)));
