<?php

	namespace Disgaea;

	class SaveData extends DataStruct	{

		protected	$_data			= "";
		protected	$_dataChunks	= array(

			// Character data
			// Pre-filled with your main team at the start, a handful of Pleinairs,
			// a large group of "Ark" characters with Laharl's picture and 1 in all stats,
			// followed by more Pleinairs, the castle NPCs, and finally the story characters
			// like Gordon and Flonne (the very last character in the list)
			'characters'		=> array( 'start' => 0x000BB8, 'length' => 0x06B8,	'type' => '\Disgaea\Data\Character',	'count' => 0x80),
			// Character data ends around 0x367B8 (+/- 6b8, math is hard at 4:30 AM)


			// There are supposedly 0x200 of these but everything after 0x190 is
			// a blank "Ark" senator (with 0 in everything of course)
			'senators'			=> array( 'start' => 0x0367B8, 'length' => 0x0020,	'type' => '\Disgaea\Data\Senator',	'count' => 0x200),


			// # of characters in current party
			// Seems to be 2bytes but max is 0x7F without Problems happening
			// Castle NPCs and such are near the end
			'charactercount'	=> array( 'start' => 0x044830, 'length' => 0x0002,	'type' => 'i'),


			'bgvolume'	=> array( 'start' => 0x044878, 'length' => 0x0001, 'type' => 'i'),
			'vovolume'	=> array( 'start' => 0x044879, 'length' => 0x0001, 'type' => 'i'),
			'sevolume'	=> array( 'start' => 0x04487a, 'length' => 0x0001, 'type' => 'i'),


			// Save Shop, Music Shop, Ark (? filler)
			// These characters are regenerated if they are not the correct class?
			'extranpcs'			=> array( 'start' => 0x045188, 'length' => 0x06B8,	'type' => '\Disgaea\Data\Character',	'count' => 0x03),



			// Setting to all FF didn't do anything, save files all have 00s so far
			'unused'	=> array( 'start' => 0x0465b0, 'length' => 0x22C7, 'type' => "h"),
			);


	}
