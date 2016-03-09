<?php

	class ChunkyFileFormat {

		public	$data			= "";
		public	$dataChunks		= array(
			);


		public function getChunk($what, $from = false) {
			if (!$from) {
				$from	= $this->data;
			}
			if (!isset($this->dataChunks[$what])) {
				var_dump($this->dataChunks);
				throw new Exception("Unknown data request: $what");
			}

			$v	= $this->dataChunks[$what];

			if (!$v['length']) {
				// "until end of file"
				// There is no way to substr() "to the end of string" if you specify a third argument
				// (not even "false" or "null" works, jfc)
				// So emulate the "get all data" by just setting the length to the string. wow
				$v['length']	= strlen($from);
			}

			$d	= substr($from, $v['start'], $v['length']);

			if ($v['type'] == "i") {
				return $this->getLEValue($d);
			} else {
				return $d;
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
