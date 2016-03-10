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

		protected	$_data			= "";
		protected	$_dataChunks	= array(
			#'name'			=> array( 'start' => 0x0000, 'length' => 0x0020,	'type' => "s"),
			'exp'				=> array( 'start' => 0x0000, 'length' => 0x0008,	'type' => 'i'),
			'equipment'			=> array( 'start' => 0x0008, 'length' => 0x0090,	'type' => '\Disgaea\Data\Item',	'count' => 4),


			#'currenthp'		=> array( 'start' => 0x0048, 'length' => 0x0004, 'type' => "i"),
			#'currentsp'		=> array( 'start' => 0x004c, 'length' => 0x0004, 'type' => "i"),
			#'stats'			=> array( 'start' => 0x0048, 'length' => \Disgaea\Data\Stats::$size, 'type' => '\Disgaea\Data\Stats'),

			// Name: 0x21 bytes
			// Title: immediately after, ? bytes

			);


	}