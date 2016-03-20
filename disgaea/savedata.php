<?php

	namespace Disgaea;

	class SaveData extends DataStruct	{

		protected	$_data			= "";
		protected	$_dataChunks	= array(
			'characters'		=> array( 'start' => 0x000BB8, 'length' => 0x06B8,	'type' => '\Disgaea\Data\Character',	'count' => 0x80),

			// # of characters in current party
			// Seems to be 2bytes but max is 0x7F without Problems happening
			// Castle NPCs and such are near the end
			'charactercount'	=> array( 'start' => 0x044830, 'length' => 0x0002,	'type' => 'i'),
			);


	}
