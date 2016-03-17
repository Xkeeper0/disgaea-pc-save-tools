<?php

	namespace Disgaea\Data\Stats;

	class ItemBaseStats extends \Disgaea\Data\Stats {

		public static $size			= 0x10;
		protected	$_data			= "";
		protected	$_dataChunks	= array(
			'hp'			=> array( 'start' => 0x0000, 'length' => 0x0002, 'type' => "i"),
			'sp'			=> array( 'start' => 0x0002, 'length' => 0x0002, 'type' => "i"),
			'atk'			=> array( 'start' => 0x0004, 'length' => 0x0002, 'type' => "i"),
			'def'			=> array( 'start' => 0x0006, 'length' => 0x0002, 'type' => "i"),
			'int'			=> array( 'start' => 0x0008, 'length' => 0x0002, 'type' => "i"),
			'spd'			=> array( 'start' => 0x000a, 'length' => 0x0002, 'type' => "i"),
			'hit'			=> array( 'start' => 0x000c, 'length' => 0x0002, 'type' => "i"),
			'res'			=> array( 'start' => 0x000e, 'length' => 0x0002, 'type' => "i"),
			);

	}