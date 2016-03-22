<?php

	$file	= file_get_contents($argv[1]);
	$len	= strlen($file);

	chdir("../");
	include "utils.php";
	
	print "Now is the time of parsing!! Ready! Parser X!\nToday's file is $argv[1].\n\n";

	$script	= new Script($file);
	$script->parse();

	die("\n----------------------------------\nEnd\n\n");



	class Script {

		protected	$_data	= "";
		protected	$_ptr	= 0;
		protected	$_len	= 0;

		public function __construct($d) {
			$this->_data	= $d;
			$this->_ptr		= 0;
			$this->_len		= strlen($d);
		}


		public function parse() {

			while ($this->_ptr < $this->_len) {

				$opcode		= $this->_ri();
				printf("%02x: ", $opcode);

				switch ($opcode) {

					case 0x02:
						print "End of script";
						// No-op
						$this->_r();

						break;


					case 0x32:
						$tl		= $this->_ri();
						$ta		= $this->_getArgsB($tl);
						$text	= sjis($ta);
						printf("Display text: \"%s\"", $text);

						break;



					case 0x5c:
						$argc	= $this->_ri();
						$argv	= $this->_getArgsI($argc);
						printf("Set text display color?   [%s]", $this->_prettyArgs($argv));

						break;




					default:
						$argc	= $this->_ri();
						$argv	= $this->_getArgsI($argc);
						printf("Unknown opcode %02x  args[%s]", $opcode, $this->_prettyArgs($argv));

						break;

				}

				print "\n";

			}

		}



		protected function _getArgsI($n) {
			$args	= array();
			for ($i = 0; $i < $n; $i++) {
				$args[]	= $this->_ri();
			}

			return $args;
		}


		protected function _getArgsB($n) {
			$args	= "";
			for ($i = 0; $i < $n; $i++) {
				$args	.= $this->_r();
			}

			return $args;
		}



		protected function _prettyArgs($args, $format = "%02x") {
			$out	= array();
			foreach ($args as $arg) {
				$out[]	= sprintf($format, $arg);
			}
			return implode(", ", $out);
		}


		protected function _r() {
			if ($this->_ptr > $this->_len) {
				throw new \Exception("Pointer exceeded data length");
			}
			$o		= substr($this->_data, $this->_ptr, 1);
			$this->_ptr++;
			return $o;
		}

		protected function _ri() {
			return ord($this->_r());
		}


	}
