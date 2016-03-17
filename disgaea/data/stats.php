<?php

	namespace Disgaea\Data;

	class Stats extends \Disgaea\DataStruct {

		public static $size			= 0x20;
		protected	$_data			= "";
		protected	$_dataChunks	= array(
			'hp'			=> array( 'start' => 0x0000, 'length' => 0x0004, 'type' => "i"),
			'sp'			=> array( 'start' => 0x0004, 'length' => 0x0004, 'type' => "i"),
			'atk'			=> array( 'start' => 0x0008, 'length' => 0x0004, 'type' => "i"),
			'def'			=> array( 'start' => 0x000c, 'length' => 0x0004, 'type' => "i"),
			'int'			=> array( 'start' => 0x0010, 'length' => 0x0004, 'type' => "i"),
			'spd'			=> array( 'start' => 0x0014, 'length' => 0x0004, 'type' => "i"),
			'hit'			=> array( 'start' => 0x0018, 'length' => 0x0004, 'type' => "i"),
			'res'			=> array( 'start' => 0x001c, 'length' => 0x0004, 'type' => "i"),
			);


		public function __toString() {
			
			$out	= "";
			foreach ($this->_dataChunks as $chunk => $d) {
				$out	.= ($out ? " - " : "") . strtoupper($chunk) .": ". $this->getChunk($chunk);
			}

			return $out;
		}

	}