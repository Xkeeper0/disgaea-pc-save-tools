<?php

	namespace Disgaea\Data;


	class Senator extends \Disgaea\DataStruct {

		public static $size			= 0x020;
		protected	$_data			= "";
		protected	$_dataChunks	= array(

			'level'				=> array( 'start' => 0x0000, 'length' => 0x0002, 'type' => 'i'),
			'class'				=> array( 'start' => 0x0002, 'length' => 0x0002, 'type' => 'i'),

			'attendance'		=> array( 'start' => 0x0004, 'length' => 0x0002, 'type' => 'i'),
			'killed'			=> array( 'start' => 0x0006, 'length' => 0x0002, 'type' => 'i'),

			// Possibly "uniquer"
			'namebank'			=> array( 'start' => 0x0008, 'length' => 0x0001, 'type' => 'i'),
			'nameindex'			=> array( 'start' => 0x0009, 'length' => 0x0001, 'type' => 'i'),
			
			// Unknown value, always seems to be 0?
			'unknown'			=> array( 'start' => 0x000A, 'length' => 0x0001, 'type' => 'i'),

			// This seems to be another magic number like "favor"
			// Values are around 0x100 +/- some amount
			// Setting it all to the same gives the same desire across all senators for a given rarity
			// How it works is not known exactly but who cares
			'raritypreference'	=> array( 'start' => 0x000B, 'length' => 0x0002, 'type' => 'i'),

			'name'				=> array( 'start' => 0x000D, 'length' => 0x0010, 'type' => 's'),

			// This is actually + 0x7F & 0x80; so a max "love" will be 17F, max loathe 80
			// Probably so game can add +/- value to vote and test bit 8 to see if they vote yes
			'favor'				=> array( 'start' => 0x001E, 'length' => 0x0002, 'type' => "i"),

			);


		public function getFavorText() {
			$f		= $this->getChunk('favor');
			$f		= ($f & 0x7F) - 0x80;

			$out	= "???";

			if     ($f < -40)	$out	= "Loathe";
			elseif ($f < -26)	$out	= "Total opposition";
			elseif ($f < -16)	$out	= "Strongly against";
			elseif ($f < -11)	$out	= "Against";
			elseif ($f <  -5)	$out	= "Leaning no";
			elseif ($f <   4)	$out	= "Either way";
			elseif ($f <  11)	$out	= "Leaning yes";
			elseif ($f <  15)	$out	= "In favor of";
			elseif ($f <  25)	$out	= "Strongly for";
			elseif ($f <  39)	$out	= "Total support";
			elseif ($f < 127)	$out	= "Love";


			return sprintf("(%4d) %s", $f, $out);
		}



		public function __toString() {
			try {
			return sprintf("Lv %4d, %-16s - attend %5d - killed %5d - unk %02x - %-25s - %s", 
				$this->getChunk("level"),
				\Disgaea\Data\Id::getClass($this->getChunk("class")),
				$this->getChunk("attendance"),
				$this->getChunk("killed"),
				$this->getChunk("unknown"),
				$this->getFavorText(),
				$this->getChunk("name")
				);
			} catch (\Exception $e) {
				return $e->getMessage();
			}
		}

	}
