<?php

	namespace Disgaea\Data;

	class Innocent extends \Disgaea\DataStruct {

		protected	$_data			= "";
		protected	$_dataChunks	= array(
			#'name'			=> array( 'start' => 0x0000, 'length' => 0x0020,	'type' => "s"),
			'level'			=> array( 'start' => 0x0000, 'length' => 0x0002, 'type' => "i"),
			'type'			=> array( 'start' => 0x0002, 'length' => 0x0001, 'type' => "i"),
			'uniquer'		=> array( 'start' => 0x0003, 'length' => 0x0001, 'type' => "i"),

			);


		public function __construct($data) {

			// 0x90 bytes
			// In SAVE000.DAT, laharl's equipment appears to start at 0x0BC0
			$this->_data	= $data;
		}



		public function dump() {
			"";
		}


		public function __toString() {
			if ($this->getChunk("type")) {
				return "Innocent [". \Disgaea\Data\ID::getInnocent($this->getChunk("type")) ."], level ". $this->getChunk("level") .", uniqueifier ". $this->getChunk('uniquer');

			} else {
				return "(None)";
			}
		}

	}