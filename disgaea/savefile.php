<?php

	namespace Disgaea;

	class SaveFile extends DataStruct {

		protected	$_data			= "";
		protected	$_saveData		= null;
		protected	$_dataChunks		= array(
			'unknown0'			=> array( 'start' => 0x0000, 'length' => 0x0020,	'type' => "s"),
			'xorkey'			=> array( 'start' => 0x0020, 'length' => 0x0004,	'type' => "s"),
			'unknown1'			=> array( 'start' => 0x0024, 'length' => 0x0002,	'type' => "i"),
			'unknown2'			=> array( 'start' => 0x0026, 'length' => 0x0002,	'type' => "i"),
			'unknown3'			=> array( 'start' => 0x0028, 'length' => 0x0004,	'type' => "i"),
			'length'			=> array( 'start' => 0x002c, 'length' => 0x0004,	'type' => "i"),
			'data'				=> array( 'start' => 0x0030, 'length' => false,		'type' => "s"),
			'magic'				=> array( 'start' => 0x0030, 'length' => 0x0008,	'type' => "s" ),
			'unknown4'			=> array( 'start' => 0x0038, 'length' => 0x0004,	'type' => "i" ),
			'compressedSize'	=> array( 'start' => 0x003c, 'length' => 0x0004,	'type' => "i" ),
			'decompressedSize'	=> array( 'start' => 0x0040, 'length' => 0x0004,	'type' => "i" ),
			'compressedData'	=> array( 'start' => 0x0044, 'length' => false,		'type' => "s" ),
			);


		public function __construct($f) {
			if (file_exists($f)) {
				$encryptedData	= file_get_contents($f);
			} else {
				throw new \Exception("File $f doesn't exist");
			}

			$this->_data	= $this->decrypt($encryptedData);
		}

		public function decrypt($encryptedData) {

			$key		= $this->getChunk("xorkey", $encryptedData);
			$saveData	= $encryptedData;
			$dataLen	= strlen($saveData);
			$keyLen		= strlen($key);

			$o			= "";
			for ($i	= 0; $i < $dataLen; $i++) {

				if (
					($i >= 0x0020 && $i <= 0x0023) ||
					($i >= 0x0030)
				) {
					// Key applies to the 'key' and 'data' areas only
					$v	= ord($saveData{$i}) ^ ord($key{$i % $keyLen});
				} else {
					// Raw data
					$v	= ord($saveData{$i});
				}

				$o	.= chr($v);

			}

			return $o;

		}


		public function getSaveObject() {
			$magic			= $this->getChunk("magic");
			if ($magic != "YKCMP_V1") {
				throw new Exception("Unexpected magic value: ". $magic);
			}

			return new CompressionHandler($this->getChunk("compressedData"), $this->getChunk("decompressedSize"));

		}


		public function getRawSaveData() {
			return $this->getChunk("data");
		}


	}

