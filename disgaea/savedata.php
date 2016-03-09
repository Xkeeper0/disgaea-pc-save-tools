<?php

	namespace Disgaea;

	class SaveData extends ChunkyFileFormat	{

		public	$data			= "";
		public	$dataChunks		= array(
			);


		public function __construct($data) {
			$this->data	= $data;


		}



	}
