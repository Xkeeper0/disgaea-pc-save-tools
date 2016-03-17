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
		$str	= iconv("shift-jis", "utf8", trim($str));
		return Normalizer::normalize($str, Normalizer::NFKC);
	}


	function unimplemented($m) {
		throw new \Exception("Unimplemented: $m");
	}




	function dumpSaveStuff($s, $s2 = null) {

		$read		= array(
			'unknown0'			=> "h",
			'xorkey'			=> "h",
			'magic'				=> "h",
			'unknown1'			=> "i",
			'unknown2'			=> "i",
			'lengthdiv4'		=> "i",
			'length'			=> "i",
			'unknown4'			=> "i",
			'decompressedSize'	=> "i",
			'data'				=> "l",
			'compressedSize'	=> "i",
			'compressedData'	=> "l",
			);

		foreach ($read as $chunk => $type) {

			$d	= $s->getChunk($chunk);
			if ($s2) $d2	= $s2->getChunk($chunk);

			printf("%-20s: ", $chunk);

			if ($type == "h") {
				printf("%-25s | %-25s ", bin2hex($d), ($s2 ? bin2hex($d2) : ""));

			} elseif ($type == "l") {
				printf("%8d (%8x) bytes | ", strlen($d),  strlen($d));
				if ($s2) printf("%8d (%8x) bytes",       strlen($d2), strlen($d2));

			} else {
				printf("%8d (%8x)       | ", $d, $d);
				if ($s2) printf("%8d (%8x) ", $d2, $d2);

			}

			print "\n";
		}

	}









	function generateHTMLMap($map) {

?>
<style type="text/css">
pre { font-family: Ubuntu Mono, Consolas, monospace;  }
* {
	line-height:	150%;
}

span:hover {
	background:	white;
}
<?php
	$tc	= count($map['types']);
	$fq	= 0.1;
	$n	= 0;

	foreach($map['types'] as $type => $count) {

		for ($i = 0; $i < $count; $i++) {
			$rr	= sin($fq * $n      ) * 30 + 220;
			$rg	= sin($fq * $n + 2.0) * 30 + 220;
			$rb	= sin($fq * $n + 4.0) * 30 + 220;

			$br	= $rr - 80;
			$bg	= $rg - 80;
			$bb	= $rb - 80;

			printf(".%s-%d { background: #%02x%02x%02x; border-top: 1px solid; border-bottom: 1px solid; border-color: #%02x%02x%02x; }\n", $type, $i, $rr, $rg, $rb, $br, $bg, $bb);

			$n	+= .2;
		}

		$n	+= 7;

	}
	print "</style><pre>";

	$c	= count($map['map']);

	$last	= "";


	for ($i = 0; $i < $c; $i++) {

		$m	= $map['map'][$i];
		if (($i % 0x20) === 0) {
			printf("\n%04x | ", $i);
		}	

		$t	= $m['type'] ."-". $m['n'];
		if ($t != $last) {
			if ($last) {
				print "</span> ";
			}
			print "<span class='$t' title='$t'>";
		} else {
			print " ";
		}

		$last	= $t;

		print $m['v'];

	}

?>

</pre>

<?php

	}