<?php

	namespace Disgaea;

	class SaveData extends DataStruct	{

		protected	$_data			= "";
		protected	$_dataChunks	= array(
			'characters'		=> array( 'start' => 0x0BB8, 'length' => 0x06B8,	'type' => '\Disgaea\Data\Character',	'count' => 0x7F),
			);


	}
