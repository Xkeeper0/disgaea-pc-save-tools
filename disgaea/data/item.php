<?php

	namespace Disgaea\Data;

	class Item extends \Disgaea\DataStruct {

		public static $size			= 0x90;
		protected	$_data			= "";
		protected	$_dataChunks	= array(
			#'name'			=> array( 'start' => 0x0000, 'length' => 0x0020,	'type' => "s"),

			// Item format on PSP -- seems to be the same here
			// http://www.gamefaqs.com/boards/935234-disgaea-afternoon-of-darkness/40932453
			'innocents'		=> array( 'start' => 0x0000, 'length' => 0x0004, 'type' => '\Disgaea\Data\Innocent', 'count' => 16),
			'price'			=> array( 'start' => 0x0040, 'length' => 0x0008, 'type' => "i"),

			// Stats
			'stats'			=> array( 'start' => 0x0048, 'length' => 0x0020, 'type' => '\Disgaea\Data\Stats'),
			'basestats'		=> array( 'start' => 0x0068, 'length' => 0x0010, 'type' => '\Disgaea\Data\Stats\ItemBaseStats'),

			// ID of name
			'name'			=> array( 'start' => 0x0078, 'length' => 0x0002, 'type' => "i"),
			'level'			=> array( 'start' => 0x007A, 'length' => 0x0002, 'type' => "i"),
			'lastfloor'		=> array( 'start' => 0x007C, 'length' => 0x0002, 'type' => "i"),
			'rarity'		=> array( 'start' => 0x007E, 'length' => 0x0001, 'type' => "i"),

			'type'			=> array( 'start' => 0x007F, 'length' => 0x0001, 'type' => "i"),
			'icon'			=> array( 'start' => 0x0080, 'length' => 0x0001, 'type' => "i"),

			'maxpop'		=> array( 'start' => 0x0081, 'length' => 0x0001, 'type' => "i"),
			'mv'			=> array( 'start' => 0x0082, 'length' => 0x0001, 'type' => "i"),
			'jm'			=> array( 'start' => 0x0083, 'length' => 0x0001, 'type' => "i"),
			'rank'			=> array( 'start' => 0x0084, 'length' => 0x0001, 'type' => "i"),


			'unknown04'		=> array( 'start' => 0x007F, 'length' => 0x0011, 'type' => "h"),

			);



		public function dump() {
			foreach ($this->_dataChunks as $chunk => $_) {
				$data	= $this->getChunk($chunk);
				if ($chunk == "id") {
					$data	= sprintf("%04X [%s]", $data, \Disgaea\Data\ID::getItem($data));
				}
				$datastring	= $data;
				if (is_array($data)) {
					$datastring	= "\n";
					foreach ($data as $id => $d) {
						$datastring	.= "    $id: $d\n";
					}
				}
				printf("%-20s %s\n", $chunk, $datastring);
			}
		}

	}