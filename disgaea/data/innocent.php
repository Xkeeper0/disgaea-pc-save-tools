<?php

	namespace Disgaea\Data;

	class Innocent extends \Disgaea\DataStruct {

		public static $size			= 0x04;
		protected	$_data			= "";
		protected	$_dataChunks	= array(
			'level'			=> array( 'start' => 0x0000, 'length' => 0x0002, 'type' => "i"),
			'type'			=> array( 'start' => 0x0002, 'length' => 0x0001, 'type' => "i"),
			'uniquer'		=> array( 'start' => 0x0003, 'length' => 0x0001, 'type' => "i"),
			);


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