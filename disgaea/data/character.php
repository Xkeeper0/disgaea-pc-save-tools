<?php

	namespace Disgaea\Data;

	class Character extends \Disgaea\DataStruct {

		protected	$_data			= "";
		protected	$_dataChunks	= array(
			#'name'			=> array( 'start' => 0x0000, 'length' => 0x0020,	'type' => "s"),


			// Name: 0x21 bytes
			// Title: immediately after, ? bytes


			);


		public function __construct($data) {
			$this->_data	= $data;
		}

	}