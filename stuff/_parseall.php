<?php

	$s	= scandir("scripts/");
	$n	= count($s) - 2;
	$d	= 0;
	foreach ($s as $f) {
		if ($f{0} !== "." && substr($f, -4) === ".bin") {
			$d++;
			print "$f ($d / $n)\n";
			shell_exec("php parsescript.php scripts/$f > scripts/p/$f.txt");
		}
	}
