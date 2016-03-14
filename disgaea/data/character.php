<?php

	namespace Disgaea\Data;



	/*
	Laharl character data:
	real	from base
	000bb0	000000	?
	000bb8	000008	EXP (8 bytes)
	000bc0	000010	item data (0x90 * 4)
	00117c	0005cc	stats (4 bytes) - HP/SP/HP/SP/ATK etc...
	001208	000658	weapon mastery (6 bytes)

	*/

	class Character extends \Disgaea\DataStruct {

		public static $size			= 0x684;
		protected	$_data			= "";
		protected	$_dataChunks	= array(

			'exp'				=> array( 'start' => 0x0000, 'length' => 0x0008, 'type' => 'i'),
			'equipment'			=> array( 'start' => 0x0008, 'length' => 0x0090, 'type' => '\Disgaea\Data\Item', 'count' => 4),

			'name'				=> array( 'start' => 0x0248, 'length' => 0x0020, 'type' => "s"),
			'unknown01'			=> array( 'start' => 0x0268, 'length' => 0x0001, 'type' => "i"),
			'title'				=> array( 'start' => 0x0269, 'length' => 0x001a, 'type' => "s"),
			'unknown02'			=> array( 'start' => 0x028a, 'length' => 0x0002, 'type' => "i"),
			
			'unknown03'			=> array( 'start' => 0x028c, 'length' => 0x0020, 'type' => "h"),
			
			'resistances'		=> array( 'start' => 0x02ac, 'length' => 0x0002, 'type' => "i", 'count' => 0x0005),
			
			'unknown04'			=> array( 'start' => 0x02b6, 'length' => 0x006e, 'type' => "h"),
			
			// todo: split this into its own class, probably
			'skillexp'			=> array( 'start' => 0x0324, 'length' => 0x0004, 'type' => "i", 'count' => 0x0060),
			'skills'			=> array( 'start' => 0x04a4, 'length' => 0x0002, 'type' => "i", 'count' => 0x0060),
			'skilllevel'		=> array( 'start' => 0x0564, 'length' => 0x0001, 'type' => "i", 'count' => 0x0060),
			
			
			'currenthp'			=> array( 'start' => 0x05c4, 'length' => 0x0004, 'type' => "i"),
			'currentsp'			=> array( 'start' => 0x05c8, 'length' => 0x0004, 'type' => "i"),
			'stats'				=> array( 'start' => 0x05cc, 'length' => 0x0020, 'type' => '\Disgaea\Data\Stats'),
			'realstats'			=> array( 'start' => 0x05ec, 'length' => 0x0020, 'type' => '\Disgaea\Data\Stats'),
			
			'unknown05'			=> array( 'start' => 0x060c, 'length' => 0x0020, 'type' => "h"),
			
			'mana'				=> array( 'start' => 0x062c, 'length' => 0x0004, 'type' => "i"),
			
			'unknown06'			=> array( 'start' => 0x0630, 'length' => 0x0004, 'type' => "i"),
			'unknown07'			=> array( 'start' => 0x0634, 'length' => 0x000C, 'type' => "h"),
			'unknown08'			=> array( 'start' => 0x0640, 'length' => 0x0018, 'type' => "h"),
			
			'basestats'			=> array( 'start' => 0x0658, 'length' => 0x0008, 'type' => '\Disgaea\Data\Stats\CharacterBaseStats'),
			
			'unknown09'			=> array( 'start' => 0x0660, 'length' => 0x0024, 'type' => "h"),

			);



		public function __toString() {
			return sprintf("%-16s / %-16s: Lv. %4d, HP %d/%d, SP %d/%d", 
				$this->getChunk("name"),
				$this->getChunk("title"),
				"0",
				$this->getChunk("currenthp"),
				$this->getChunk("stats")->getChunk("hp"),
				$this->getChunk("currentsp"),
				$this->getChunk("stats")->getChunk("sp")
				);
		}

	}