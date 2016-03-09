<?php

	namespace Disgaea;

	class DataStruct {

		protected	$_data			= "";
		protected	$_dataChunks	= array(
			/*
			Name of chunk              Starting offset    Length                 Data type
			'name'			=> array( 'start' => 0x0000, 'length' => 0x0020,	'type' => "s"),

			type:
				i	int (treated as a little-endian number)
				s	string (treated as raw bytes)
				(name of class) returns new <class>(data)
			*/
			);


		public function getChunk($what, $from = false) {
			if (!$from) {
				$from	= $this->_data;
			}
			if (!isset($this->_dataChunks[$what])) {
				var_dump($this->_dataChunks);
				throw new \Exception("Unknown data request: $what");
			}

			$v	= $this->_dataChunks[$what];

			if (!$v['length']) {
				// "until end of file"
				// There is no way to substr() "to the end of string" if you specify a third argument
				// (not even "false" or "null" works, jfc)
				// So emulate the "get all data" by just setting the length to the string. wow
				$v['length']	= strlen($from);
			}

			$d	= substr($from, $v['start'], $v['length']);


			// Return data in a meaningful way
			if ($v['type'] == "i" || !$v['type']) {
				// "int" type; turn into number and return
				return $this->getLEValue($d);

			} elseif ($v['type'] == "s") {
				// "string" type; return unchanged bytes
				return $d;

			} elseif ($v['type'] == "h") {
				// return hexadecimal representation of bytes (debug)
				// complicated shit to turn it into XX XX XX XX... format
				// Probably a simpler way but, 1:50 AM, who cares
				return implode(" ", str_split(bin2hex($d), 2));

			} else {
				// "class" type: return a new class constructed with the byte data
				return new $v['type']($d);
			}

		}



		public function getLEValue($s) {
			$t	= str_split($s);
			$o	= 0;
			foreach ($t as $i => $v) {
				$o	+= ord($v) << ($i * 8);
			}

			return $o;

		}

}
