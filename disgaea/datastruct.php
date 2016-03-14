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


		public function __construct($data) {
			$this->_data	= $data;


			if (method_exists($this, '_init')) {
				// Silly workaround to prevent having to build new constructors
				// that call parent::__construct() all the time
				$this->_init();
			}
		}


		/**
		 * Get a piece of information from the raw data
		 * $what refers to the $_dataChunks property
		 * $from can be used to select an alternate source
		 */
		public function getChunk($what, $from = null) {

			// Determine source of data
			if (!$from) {
				$from	= $this->_data;
			}

			// Determine what data we are going to get
			if (!isset($this->_dataChunks[$what])) {
				var_dump($this->_dataChunks);
				throw new \Exception("Unknown data request: $what");
			}

			// Temporary variable for convinience
			$v	= $this->_dataChunks[$what];

			// Determine length of requested data
			// If no length, assume "all remaining in file"
			if (!$v['length']) {

				if (isset($v['count']) && $v['count'] > 1) {
					// Check for absurdity
					throw new \Exception("Trying to arrayize the remainder of the data is a bad idea, don't do that.");
				}

				// There is no way to substr() "to the end of string" if you specify a third argument
				// (not even "false" or "null" works, jfc)
				// So emulate the "get all data" by just setting the length to the string. wow
				$v['length']	= strlen($from);

			} elseif ($v['length'] === "*") {
				// "*" is a special value saying "get the size from the referenced class"
				// Probably a better way to do this but, lazy
				// since you can't use this in an actual class definition
				$v['length']	= $v['type']::$size;
			}


			// Handle getting an array of chunks
			// e.g., someone's equipment, which is 4 "item" chunks in sequence
			if (isset($v['count']) && $v['count'] > 1) {
				// Return an array of values based on count tag
				$out	= array();
				for ($i = 0; $i < $v['count']; $i++) {
					$d	= substr($from, $v['start'] + $v['length'] * $i, $v['length']);
					$out[$i]	= $this->_getChunkValue($d, $v['type']);
				}

				return $out;

			} else {
				// Single element, just get the data for one
				$d	= substr($from, $v['start'], $v['length']);
				return $this->_getChunkValue($d, $v['type']);

			}
		}



		/**
		 * Return all chunks in an array
		 */
		public function getAllChunks($from = null) {

			$out	= array();

			foreach ($this->_dataChunks as $chunk => $v) {

				$dataP			= $this->getChunk($chunk, $from);

				if ($dataP instanceof \Disgaea\DataStruct) {
					// Recursively fetch further object chunks
					$data		= $dataP->getAllChunks();

				} elseif (is_array($dataP)) {
					// Recursively recurse because (foams at mouth)
					foreach ($dataP as &$dataPValue) {
						if ($dataPValue instanceof \Disgaea\DataStruct) {
							$dataPValue	= $dataPValue->getAllChunks();
						}
					}
					$data	= $dataP;

				} else {
					$data		= $dataP;
				}
				$out[$chunk]	= $data;

			}

			return $out;

		}


		/**
		 * Get the actual value for a chunk of data
		 * $data is the raw string of bytes
		 * $type is the defined type (or class) to use
		 */
		protected function _getChunkValue($data, $type) {

			// Return data in a meaningful way
			switch ($type) {

				case "i":
					// "int" type; turn into number and return
					return $this->getLEValue($data);
					break;

				case "b":
					// "binary" type; return unchanged bytes
					return $data;
					break;

				case "s":
					// "string" type; return SJIS-decoded text
					return sjis(trim($data));
					break;

				case "h":
					// return hexadecimal representation of bytes (debug)
					return implode(" ", str_split(bin2hex($data), 2));
					break;

				default:
					if (class_exists($type)) {
						return new $type($data);
					} else {
						throw new \Exception("Unknown chunk type '$type'");
					}
					break;
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



		public function getMap() {

			$map	= array();
			$l		= strlen($this->_data);
			for ($i = 0; $i < $l; $i++) {
				$map[$i]	= array('v' => sprintf("%02x", ord($this->_data{$i})), 'type' => null, 'n' => 0);
			}

			$types	= array();

			foreach ($this->_dataChunks as $type => $chunk) {


				$start	= $chunk['start'];

				// Get length of this chunk of data...
				if ($chunk['length'] !== "*") {
					$length	= $chunk['length'];
				} else {
					$length	= $chunk['type']::$size;
				}

				// If it's an array, multiply by # of elements
				if (isset($chunk['count'])) {
					$count	= $chunk['count'];
				} else {
					$count	= 1;
				}

				$types[$type]	= $count;

				for ($i = 0; $i < ($length * $count); $i++) {
					$o	= $start + $i;
					if ($map[$o]['type'] !== null) {
						throw new \Exception("Data overlap! Offset ". sprintf("%04x", $o) ." is marked as both ". $map[$o]['type'] ." and ". $type ."!");
					}

					$map[$o]['type']	= $type;
					$map[$o]['n']		= floor($i / $chunk['length']);
				}

			}

			return array("map" => $map, "types" => $types);

		}


	}
