<?php

	namespace Disgaea\Data\Stats;

	class CharacterBaseStats extends \Disgaea\DataStruct {

		public static $size			= 0x08;
		protected	$_data			= "";
		protected	$_dataChunks	= array(
			'hp'			=> array( 'start' => 0x0000, 'length' => 0x0001, 'type' => "i"),
			'sp'			=> array( 'start' => 0x0001, 'length' => 0x0001, 'type' => "i"),
			'atk'			=> array( 'start' => 0x0002, 'length' => 0x0001, 'type' => "i"),
			'def'			=> array( 'start' => 0x0003, 'length' => 0x0001, 'type' => "i"),
			'int'			=> array( 'start' => 0x0004, 'length' => 0x0001, 'type' => "i"),
			'spd'			=> array( 'start' => 0x0005, 'length' => 0x0001, 'type' => "i"),
			'hit'			=> array( 'start' => 0x0006, 'length' => 0x0001, 'type' => "i"),
			'res'			=> array( 'start' => 0x0007, 'length' => 0x0001, 'type' => "i"),
			);

	}