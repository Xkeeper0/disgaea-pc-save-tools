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
		protected	$_depth	= 0;

		public function __construct($d) {
			$this->_data	= $d;
			$this->_ptr		= 0;
			$this->_len		= strlen($d);
		}


		protected function _indent() {
			print str_repeat("    ", $this->_depth);
		}

		public function parse() {

			while ($this->_ptr < $this->_len) {

				$opcode		= $this->_ri();
				printf("%03d: ", $opcode);

				if ($opcode == 0x09) {
					// Gross hack: 09 is the "end if" block, so make it go back a level first
					$this->_depth--;
					$this->_indent();
					$this->_depth++;
				} else {
					$this->_indent();
				}

				switch ($opcode) {

					case 0x02:
						$this->_genericOpcode("End of script");
						break;


					case 0x05:
						$this->_genericOpcode("Start of script");
						break;


					case 0x07:
						// Distance from this opcode to the next "out of this blocK" opcode?
						$dist		= $this->_ri();
						$dist		+= $this->_ri() * 0x100;
						$this->_depth++;

						print "If (";
						$this->_getConditions();
						print ")";

						break;


					case 0x08:
						// Do something. Very useful
						$len	= $this->_ri();
						printf("Operation (%02x): ", $len);
						// This byte is ignored here but should always be 
						$this->_getConditions(1);

						break;


					case 0x09:
						$this->_depth--;
						print "End If";
						$this->_r();
						break;


					case 0x12:
						$argc	= $this->_ri();
						$argv	= $this->_getArgsB($argc);
						$sid	= \Disgaea\DataStruct::getLEValue($argv);
						print "Call Script: $sid";

						break;


					case 0x32:
						$tl		= $this->_ri();
						$ta		= $this->_getArgsB($tl);
						$text	= sjis($ta);
						printf("Display text: \"%s\"", $text);
						break;


					case 0x4f:
						$argc		= $this->_ri();
						$argv		= $this->_getArgsB($argc);
						$class		= \Disgaea\DataStruct::getLEValue(substr($argv, 0, 2));
						if ($class >= 0x8000) {
							$class	-= 0x10000;
						}
						$level		= \Disgaea\DataStruct::getLEValue(substr($argv, 2, 2));
						$classn		= \Disgaea\Data\Id::getClass(abs($class));
						printf("Add character: %s (%d), Lv %d", $classn, $class, $level);
						break;


					case 0x5c:
						$argc	= $this->_ri();
						$argv	= $this->_getArgsI($argc);
						printf("Set text display color?   [%s]", $this->_prettyArgs($argv));

						break;



					case 0xdd:
						$tl		= $this->_ri();
						$ta		= $this->_getArgsB($tl);
						$text	= sjis($ta);
						printf("Set title?: \"%s\"", $text);
						break;




					default:
						$this->_genericOpcode(sprintf("Unknown opcode %02x", $opcode));
						break;

				}

				print "\n";

			}

		}


		protected function _genericOpcode($msg) {
			$argc	= $this->_ri();
			$argv	= $this->_getArgsI($argc);
			printf("%s - args[%s]", $msg, $this->_prettyArgs($argv));
		}



		protected function _getConditions($n = null) {
			if ($n === null) {
				$num	= $this->_ri();
			} else {
				$num	= $n;
			}
			#$this->_indent();
			#if ($num > 1) print "   ";
			for ($i = 0; $i < $num; $i++) {
				$operand1	= $this->_getOperand();
				$operator	= $this->_getOperator();
				$operand2	= $this->_getOperand();

				print "$operand1 $operator $operand2";
				#$this->_indent();

				if ($i < ($num - 1)) {
					print " ". $this->_getOperator() ." ";
				}
			}

		}


		protected function _getOperator() {
			$operator	= $this->_ri();
			switch ($operator) {
				case 1:
					return "=";
					break;

				case 10:
					return "==";
					break;

				case 20:
					return "||";
					break;

				default:
					return "?? ($operator)";
					break;
			}
		}

		protected function _getOperand() {
			$type	= $this->_ri();
			$value	= $this->_ri();
			$value	+= $this->_ri() * 0x100;

			switch ($type) {
				case 0x00:
					return "$value";
					break;

				case 0x01:
					return "FL#$value";
					break;


				// 04 = character?

				default:
					return "unknown ($type) $value";
					break;
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
