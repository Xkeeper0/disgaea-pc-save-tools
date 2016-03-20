<?php

	namespace Disgaea;

	class DataStruct {

		protected	$_data			= "";
		protected	$_parent		= null;
		protected	$_index			= null;

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


		public function __construct($data, $parent = null, $index = null) {
			$this->_data	= $data;
			$this->_parent	= $parent;
			$this->_index	= $index;



			if (method_exists($this, '_init')) {
				// Silly workaround to prevent having to build new constructors
				// that call parent::__construct() all the time
				$this->_init();
			}
		}


		public function setParent($obj, $chunk, $index = null) {
			$this->_parent	= array(
				'obj'	=> $obj,
				'chunk'	=> $chunk,
				);
			$this->_index	= $index;

		}


		/**
		 * Get data function.
		 * Returns $this->_data.
		 * Very useful, I assure you
		 */
		public function getData() {
			return $this->_data;
		}

		/**
		 * Set data function.
		 * Sets $this->_data.
		 * Stunningly useful, trust me
		 */
		public function setData($data) {
			$this->_data	= $data;
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
					if ($out[$i] instanceof DataStruct) {
						$out[$i]->setparent($this, $what, $i);
					}
				}

				return $out;

			} else {
				// Single element, just get the data for one
				$d	= substr($from, $v['start'], $v['length']);
				$out	= $this->_getChunkValue($d, $v['type']);
				if ($out instanceof DataStruct) {
					$out->setparent($this, $what);
				}
				return $out;

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
					return self::getLEValue($data);
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



		public static function getLEValue($s) {
			$t	= str_split($s);
			$o	= 0;
			foreach ($t as $i => $v) {
				$o	+= ord($v) << ($i * 8);
			}

			return $o;

		}


		public static function makeLEValue($v, $l, $signed = false) {

			if ($v >= (pow(256, $l))) {
				throw new \Exception("Value $v too large to store in $l bytes");
			}


			$o	= "";

			for ($i = 0; $i < $l; $i++) {
				$o	.= chr($v & 0xFF);
				$v	= $v >> 8;
			}

			return $o;

		}



		public function setChunk($what, $newValue, $index = null) {

			// Determine what data we are going to get
			if (!isset($this->_dataChunks[$what])) {
				throw new \Exception("Unknown set data request: $what");
			}

			// Temporary variable for convinience
			$v	= $this->_dataChunks[$what];

			if (isset($v['count']) && $v['count'] > 1) {
				if ($index === null) {
					unimplemented("Cannot currently write arrays of data (requested to write $what)");
				} elseif ($index >= $v['count'] || $index < 0) {
					throw new \Exception("Index ($index) out of bounds for this chunk (max ". $v['count'] .")");
				}
			}

			switch ($v['type']) {

				case "i":
					// "int" type; convert into little-endian binary value
					$newRaw		= self::makeLEValue($newValue, $v['length']);
					break;


				case "b":
					// "binary" type; insert new value in
					// Probably make sure length = new length
					if ($v['length'] && strlen($newValue) > $v['length']) {
						throw new \Exception("New data length (". strlen($newValue) .") longer than original data length (". $v['length'] .")");
					
					} elseif ($v['length'] && strlen($newValue) < $v['length']) {
						// Pad to make length equal to original data length
						$newValue	= str_pad($newValue, $v['length'], "\x00");
					}
					
					$newRaw		= $newValue;
					break;


				case "s":
					// "string" type; reconvert into fullwidth SJIS-encoded text(?)
					$newRaw	= str_pad(tosjis($newValue), $v['length'], "\x00");
					if (strlen($newRaw) > $v['length']) {
						throw new \Exception("New data length (". strlen($newRaw) .") longer than original data length (". $v['length'] .")");
					}
					break;


				case "h":
					// return hexadecimal representation of bytes (debug)
					unimplemented("Cannot currently recombobulate stringified hexadecimal");
					return implode(" ", str_split(bin2hex($data), 2));
					break;

				default:
					if ($newValue instanceof $v['type']) {
						// Get raw data from internal struct
						$newRaw	= $newValue->getData();

					} else {
						throw new \Exception("Unknown newValue given");
					}
					break;
			}


			// ACTUAL WRITING TIME
			$startOffset	= $v['start'];
			if ($index) {
				$startOffset	+= ($v['length'] * $index);
			}

			if ($v['length'] !== false) {
				if (strlen($newRaw) != $v['length']) {
					throw new \Exception("New data length (". strlen($newRaw) .") longer than original data length (". $v['length'] ."). This should probably never happen.");
				}

				// Replace all data in this range with new data
				$this->_data	= substr_replace($this->_data, $newRaw, $startOffset, $v['length']);


			} elseif ($v['length'] === false) {

				// Replace all data after start with new data
				$this->_data	= substr_replace($this->_data, $newRaw, $startOffset);

			} else {
				throw new \Exception("uhhh... this should never get here");
			}


			// Update the parent object with the new data
			$this->updateParent();

		}



		public function updateParent() {
			if ($this->_parent) {
				print "Updating parent object [". get_class($this->_parent['obj']) ."] (". $this->_parent['chunk'] ."[". ($this->_index !== null ? $this->_index : "--") ."])...\n";
				$this->_parent['obj']->setChunk($this->_parent['chunk'], $this, $this->_index);
			}

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
