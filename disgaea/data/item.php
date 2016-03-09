<?php

	namespace Disgaea\Data;

	class Item extends \Disgaea\DataStruct {

		protected	$_data			= "";
		protected	$_dataChunks	= array(
			#'name'			=> array( 'start' => 0x0000, 'length' => 0x0020,	'type' => "s"),
			'innocent01'	=> array( 'start' => 0x0000, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent02'	=> array( 'start' => 0x0004, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent03'	=> array( 'start' => 0x0008, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent04'	=> array( 'start' => 0x000c, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			/*
			'innocent05'	=> array( 'start' => 0x0010, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent06'	=> array( 'start' => 0x0014, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent07'	=> array( 'start' => 0x0018, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent08'	=> array( 'start' => 0x001c, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent09'	=> array( 'start' => 0x0020, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent10'	=> array( 'start' => 0x0024, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent11'	=> array( 'start' => 0x0028, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent12'	=> array( 'start' => 0x002c, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent13'	=> array( 'start' => 0x0030, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent14'	=> array( 'start' => 0x0034, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent15'	=> array( 'start' => 0x0038, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			'innocent16'	=> array( 'start' => 0x003c, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent'),
			*/
			// todo, find some way to make a repetitve entry

			'unknown01'		=> array( 'start' => 0x0040, 'length' => 0x0008, 'type' => "h"),

			// Stats
			'hp'			=> array( 'start' => 0x0048, 'length' => 0x0004, 'type' => "i"),
			'sp'			=> array( 'start' => 0x004c, 'length' => 0x0004, 'type' => "i"),
			'atk'			=> array( 'start' => 0x0050, 'length' => 0x0004, 'type' => "i"),
			'def'			=> array( 'start' => 0x0054, 'length' => 0x0004, 'type' => "i"),
			'int'			=> array( 'start' => 0x0058, 'length' => 0x0004, 'type' => "i"),
			'spd'			=> array( 'start' => 0x005c, 'length' => 0x0004, 'type' => "i"),
			'hit'			=> array( 'start' => 0x0060, 'length' => 0x0004, 'type' => "i"),
			'res'			=> array( 'start' => 0x0064, 'length' => 0x0004, 'type' => "i"),

			'unknown02'		=> array( 'start' => 0x0068, 'length' => 0x0010, 'type' => "h"),

			// Item ID - 2 bytes? 4?
			'id'			=> array( 'start' => 0x0078, 'length' => 0x0002, 'type' => "i"),

			'unknown03'		=> array( 'start' => 0x007A, 'length' => 0x0004, 'type' => "h"),

			// Rarity value
			'rarity'		=> array( 'start' => 0x007E, 'length' => 0x0001, 'type' => "i"),

			'unknown04'		=> array( 'start' => 0x007F, 'length' => 0x0011, 'type' => "h"),

			);


		public function __construct($data) {

			// 0x90 bytes
			// In SAVE000.DAT, laharl's equipment appears to start at 0x0BC0
			$this->_data	= $data;
		}



		public function dump() {
			foreach ($this->_dataChunks as $chunk => $_) {
				$data	= $this->getChunk($chunk);
				if ($chunk == "id") {
					$data	= sprintf("%04X [%s]", $data, \Disgaea\Data\ID::getItem($data));
				}
				printf("%-20s %s\n", $chunk, $data);
			}
		}

	}