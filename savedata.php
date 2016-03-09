<?php


	class SaveData extends ChunkyFileFormat	{

		public	$data			= "";
		public	$dataChunks		= array(
			'magic'				=> array( 'start' => 0x0000, 'length' => 0x0008,	'type' => "s" ),
			'unknown'			=> array( 'start' => 0x0008, 'length' => 0x0004,	'type' => "i" ),
			'compressedSize'	=> array( 'start' => 0x000c, 'length' => 0x0004,	'type' => "i" ),
			'decompressedSize'	=> array( 'start' => 0x0010, 'length' => 0x0004,	'type' => "i" ),
			'data'				=> array( 'start' => 0x0014, 'length' => false,		'type' => "s" ),
			);

		protected	$_compressedSaveData	= "";


		public function __construct($data) {
			$this->data	= $data;

			$magic			= $this->getChunk("magic");
			if ($magic != "YKCMP_V1") {
				throw new Exception("Unexpected magic value: ". $magic);
			}

			$this->_compressedSaveData	= $this->getChunk("data");

		}

/*
0x00 chars(8) YKCMP_V1
0x08 uInt32 unknown (0x00000004)
0x0C uInt32 compressed file size
0x10 uInt32 decompressed file size
After 0x14 are compressed data.
*/


		public function getCompressedDataObject() {
			return new YKCMP($this->_compressedSaveData, $this->getChunk("decompressedSize"));

		}


	}
