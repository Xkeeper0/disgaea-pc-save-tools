<?php


	function autoloadClass($className) {

		$path	= __DIR__ ."/". strtolower(str_replace("\\", "/", $className)) .".php";
		if (file_exists($path)) {
			require $path;
		} else {
			print "Class $className not found (looked for $path)\n";
		}

	}
	spl_autoload_register('autoloadClass');


	function sjis($str) {
		return iconv("shift-jis", "utf8", trim($str));
	}