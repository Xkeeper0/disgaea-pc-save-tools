<?php

	namespace Disgaea;

	class TestStruct extends DataStruct {

		protected	$_data			= "";
		protected	$_saveData		= null;
		protected	$_dataChunks		= array(
			'int'	=> array( 'start' => 0x0000, 'length' => 0x0004, 'type' => "i" ),
			'raw'	=> array( 'start' => 0x0004, 'length' => 0x0004, 'type' => "b" ),
			'int2'	=> array( 'start' => 0x0008, 'length' => 0x0004, 'type' => "i" ),
			'raw2'	=> array( 'start' => 0x000C, 'length' => 0x0004, 'type' => "b" ),
			'final'	=> array( 'start' => 0x0010, 'length' => false,  'type' => "b" ),
			);


		public function __construct($data) {
			// Empty
			$this->_data	= str_repeat("\x00", 0xFF);
		}

	}

