<?php

	namespace Disgaea;

	class SaveData extends DataStruct	{

		public	$data			= "";
		public	$dataChunks		= array(
			);


		public function __construct($data) {
			$this->data	= $data;


		}



	}
