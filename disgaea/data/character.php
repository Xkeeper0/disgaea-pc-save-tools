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

		public static $size			= 0x6b8;
		protected	$_data			= "";
		protected	$_dataChunks	= array(

			'exp'				=> array( 'start' => 0x0000, 'length' => 0x0008, 'type' => 'i'),
			'equipment'			=> array( 'start' => 0x0008, 'length' => 0x0090, 'type' => '\Disgaea\Data\Item', 'count' => 4),

			'name'				=> array( 'start' => 0x0248, 'length' => 0x0020, 'type' => "s"),
			'unknown01'			=> array( 'start' => 0x0268, 'length' => 0x0001, 'type' => "i"),
			'title'				=> array( 'start' => 0x0269, 'length' => 0x001a, 'type' => "s"),
			'unknown02'			=> array( 'start' => 0x028a, 'length' => 0x0002, 'type' => "i"),
			
			'unknown03'			=> array( 'start' => 0x028c, 'length' => 0x0020, 'type' => "b"),
			
			'resistances'		=> array( 'start' => 0x02ac, 'length' => 0x0002, 'type' => "i", 'count' => 0x0005),
			
			'unknown04'			=> array( 'start' => 0x02b6, 'length' => 0x006e, 'type' => "b"),
			
			// todo: split this into its own class, probably
			'skillexp'			=> array( 'start' => 0x0324, 'length' => 0x0004, 'type' => "i", 'count' => 0x0060),
			'skills'			=> array( 'start' => 0x04a4, 'length' => 0x0002, 'type' => "i", 'count' => 0x0060),
			'skilllevel'		=> array( 'start' => 0x0564, 'length' => 0x0001, 'type' => "i", 'count' => 0x0060),
			
			
			'currenthp'			=> array( 'start' => 0x05c4, 'length' => 0x0004, 'type' => "i"),
			'currentsp'			=> array( 'start' => 0x05c8, 'length' => 0x0004, 'type' => "i"),
			'stats'				=> array( 'start' => 0x05cc, 'length' => 0x0020, 'type' => '\Disgaea\Data\Stats'),
			'realstats'			=> array( 'start' => 0x05ec, 'length' => 0x0020, 'type' => '\Disgaea\Data\Stats'),
			
			'unknown05'			=> array( 'start' => 0x060c, 'length' => 0x0020, 'type' => "b"),
			
			'mana'				=> array( 'start' => 0x062c, 'length' => 0x0004, 'type' => "i"),
			
			'unknown06'			=> array( 'start' => 0x0630, 'length' => 0x0018, 'type' => "b"),

			// Levels and rate of weapon mastery. Setting mastery enables anyone to equip weapons
			// Actual WEXP not known yet
			// Mastery rate is ~0 to 30-ish, higher is better
			'weaponmasterylv'	=> array( 'start' => 0x0648, 'length' => 0x0008, 'type' => "b"),
			'weaponmasteryrate'	=> array( 'start' => 0x0650, 'length' => 0x0008, 'type' => "b"),
			
			'basestats'			=> array( 'start' => 0x0658, 'length' => 0x0008, 'type' => '\Disgaea\Data\Stats\CharacterBaseStats'),
			
			// 1 to 9999
			'level'				=> array( 'start' => 0x0660, 'length' => 0x0002, 'type' => "i"),
			// ?
			'unknown07'			=> array( 'start' => 0x0662, 'length' => 0x0002, 'type' => "b"),
			// Class
			'class'				=> array( 'start' => 0x0664, 'length' => 0x0002, 'type' => "i"),
			// "base class" - castle NPCs use the generic monster types, mages are always "Red ___",
			// etc. doesn't seem to affect anything???
			'class2'			=> array( 'start' => 0x0666, 'length' => 0x0002, 'type' => "i"),
			// Index into which row of char.dat to get skill learning data from
			// Yes this is stupid as all hell given each class has an ID *anyway*. NIS!
			'skilltree'			=> array( 'start' => 0x0668, 'length' => 0x0002, 'type' => "b"),


			'unknown08'			=> array( 'start' => 0x066a, 'length' => 0x001e, 'type' => "b"),
			'unknown09'			=> array( 'start' => 0x0688, 'length' => 0x0020, 'type' => "b"),
			'unknown10'			=> array( 'start' => 0x06a8, 'length' => 0x0010, 'type' => "b"),

			'basejm'			=> array( 'start' => 0x067a, 'length' => 0x0001, 'type' => "i"),
			'jm'				=> array( 'start' => 0x067b, 'length' => 0x0001, 'type' => "i"),
			'basemv'			=> array( 'start' => 0x067c, 'length' => 0x0001, 'type' => "i"),
			'mv'				=> array( 'start' => 0x067d, 'length' => 0x0001, 'type' => "i"),

			'counter'			=> array( 'start' => 0x067e, 'length' => 0x0001, 'type' => "i"),


			// Rank in Dark Senate
			'senaterank'		=> array( 'start' => 0x068d, 'length' => 0x0001, 'type' => "i"),


			);



		public function __toString() {
			return sprintf("%-16s / %-16s: Lv. %4d, HP %7d/%7d, SP %7d/%7d", 
				$this->getChunk("name"),
				$this->getChunk("title"),
				$this->getChunk("level"),
				$this->getChunk("currenthp"),
				$this->getChunk("stats")->getChunk("hp"),
				$this->getChunk("currentsp"),
				$this->getChunk("stats")->getChunk("sp")
				);
		}

	}