<?php

	function sjis($str) {
		return iconv("shift-jis", "utf8", trim($str));
	}