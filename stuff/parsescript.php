<?php

	if (!isset($argv[1])) {
		die("Usage: ". __FILE__ ." <script.bin>\n");

	} elseif (!file_exists($argv[1])) {
		die("File not found: $argv[1]\n");

	}

	$file	= file_get_contents($argv[1]);

	chdir("../");
	include "utils.php";

	print "Now is the time of parsing!! Ready! Parser X!\nToday's file is $argv[1].\n\n";

	$script	= new Script($file);
	$script->parse();

	die("\n----------------------------------\nEnd\n\n");


	// E_FUTURE: Move this to the \Disgaea namespace, possibly under \Data
	// Would require moving most things in \Data currently to e.g. \Save
	// Good idea, just would take time.
	class Script {

		protected	$_data	= "";				// raw script data (binary)
		protected	$_ptr	= 0;				// current read pointer
		protected	$_len	= 0;				// length of script data
		protected	$_depth	= 0;				// current control block depth

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

				// The choice of printing opcodes here in decimal (currently) may be confusing
				// NIS appears to love using decimal numbers for things, and it appears that
				// script files are no different. As they are only one byte, though,
				// it's hard to determine if they should be displayed as one or the other
				printf("%3d: ", $opcode);

				if ($opcode == 0x09) {
					// Gross hack: 09 is the "end if" block; decrease indent before printing
					$this->_depth--;
					$this->_indent();
					$this->_depth++;
				} else {
					$this->_indent();
				}


				// Most opcodes follow the format:
				// <opcode> <argc> <argv[argc]>
				// A few specific opcodes do not follow this format, however.
				// In E_FUTURE these could be class constants, but for the time being binary is useful

				switch ($opcode) {

					case 0x02:
						// Usually appears at end of script or at the end of a 07 block
						$this->_genericOpcode("End of script");
						break;


					case 0x05:
						// Often appears at the start of a script, but not always???
						$this->_genericOpcode("Start of script");
						break;


					case 0x07:
						// Starts an "If" block
						// First argument is distance to end of block, including this byte
						// (so it is 1 larger than other opcodes' "argv")
						// Right now we do not check if this actually matches the block size
						$dist		= $this->_ri();
						$dist		+= $this->_ri() * 0x100;
						$this->_depth++;

						$operation	= $this->_getOperation();
						printf("If (%s)", $operation);

						break;


					case 0x08:
						// Generally used as an assignment operator
						// e.g. "set flag X to 1"
						// Possible support for other things?
						$len		= $this->_ri();
						$operation	= $this->_getOperation(1);
						printf("Operation (%02x): %s", $len, $operation);
						break;


					case 0x09:
						// End of if block started by 07
						$this->_depth--;
						print "End If";
						$this->_r();
						break;


					case 0x12:
						// Call another script by ID
						// Assumedly exec. will return to this script afterwards
						$argc	= $this->_ri();
						$argv	= $this->_getArgsB($argc);
						$sid	= \Disgaea\DataStruct::getLEValue($argv);
						print "Call Script: $sid";
						break;


					case 0x32:
						// Used to display text for end-of-chapter preview cutscenes
						// Exact format and positioning details not fully understood
						// Point of interest: Ending scripts appear to call this with "NIS" ...???
						$tl		= $this->_ri();
						$ta		= $this->_getArgsB($tl);
						$text	= sjis($ta);
						printf("Display text: \"%s\"", $text);
						break;


					case 0x4f:
						// Adds (or removes) a character to the current party based on class ID
						// If class is negative, then remove abs(class) instead of adding it
						// If the given class exists in the "extra party" area, it will be moved (?)
						// Otherwise, a new character will be generated and added to the party
						// (Extra party area = characters in slots greater than "party size" flag;
						//  See save data parser for more information)
						$argc		= $this->_ri();
						$argv		= $this->_getArgsB($argc);
						$class		= \Disgaea\DataStruct::getLEValue(substr($argv, 0, 2));
						$action			= "Add";
						if ($class >= 0x8000) {
							$class	-= 0x10000;
							$action	= "Remove";
						}
						$level		= \Disgaea\DataStruct::getLEValue(substr($argv, 2, 2));
						$classn		= \Disgaea\Data\Id::getClass(abs($class));
						printf("%s character: %s (%d), Lv %d", $action, $classn, $class, $level);
						break;


					case 0x5c:
						// Used before 0x32 opcodes. Patterns appear to match text colors
						// Format not currently understood
						$argc	= $this->_ri();
						$argv	= $this->_getArgsI($argc);
						printf("Set text display color?   [%s]", $this->_prettyArgs($argv));
						break;


					case 0x81:
						// Gives the player an inventory item of some kind
						// First two bytes = type, second two = ??
						$argc		= $this->_ri();
						$argv		= $this->_getArgsB($argc);
						$item		= \Disgaea\DataStruct::getLEValue(substr($argv, 0, 2));
						$extra		= \Disgaea\DataStruct::getLEValue(substr($argv, 2, 2));
						$itemn		= \Disgaea\Data\Id::getItem($class);
						printf("Give item: %s (%d), extra = %04x", $itemn, $item, $extra);
						break;


					case 0xdd:
						// This apparently sets the top screen text in Disgaea DS
						// Does not appear to be used in original, PSP, or PC
						$tl		= $this->_ri();
						$ta		= $this->_getArgsB($tl);
						$text	= sjis($ta);
						printf("Set title?: \"%s\"", $text);
						break;



					default:
						// Handle unknown opcodes by simply dumping their identifiers and arguments
						$this->_genericOpcode(sprintf("Unknown opcode %02x", $opcode));
						break;

				}

				print "\n";

			}

		}


		/**
		 * Output a description and dump arguments for a given opcode
		 * No further processing is needed for these at this time
		 */
		protected function _genericOpcode($msg) {
			$argc	= $this->_ri();
			$argv	= $this->_getArgsI($argc);
			printf("%s", $msg);
			if ($argc !== 0) {
				printf(" - args[%02x]: %s", $argc, $this->_prettyArgs($argv));
			}
		}


		/**
		 * Get operation for opcode 7 and 8 (if and assignment)
		 * Handles fetching 7's dynamic number or 8's single operation
		 */
		protected function _getOperation($n = null) {
			if ($n === null) {
				$num	= $this->_ri();
			} else {
				$num	= $n;
			}

			$out	= "";

			for ($i = 0; $i < $num; $i++) {
				$operand1	= $this->_getOperand();
				$operator	= $this->_getOperator();
				$operand2	= $this->_getOperand();

				$out	.= "$operand1 $operator $operand2";

				if ($i < ($num - 1)) {
					$out	.= " ". $this->_getOperator() ." ";
				}
			}

			return $out;
		}


		/**
		 * Get the operator associated with a given operator value
		 * Could probably be a defined array in E_FUTURE
		 */
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


		/**
		 * Get the operand for a given value
		 * Not all operands are currently understood
		 */
		protected function _getOperand() {
			$type	= $this->_ri();
			$value	= $this->_ri();
			$value	+= $this->_ri() * 0x100;

			switch ($type) {
				case 0x00:
					return "$value";
					break;

				case 0x01:
					return "Flag#$value";
					break;


				// 04 = character?

				default:
					return "unknown ($type) $value";
					break;
			}
		}


		/**
		 * Get all arguments as an array of single-byte unsigned integers
		 */
		protected function _getArgsI($n) {
			$args	= array();
			for ($i = 0; $i < $n; $i++) {
				$args[]	= $this->_ri();
			}

			return $args;
		}


		/**
		 * Get all arguments as a raw string of binary data
		 */
		protected function _getArgsB($n) {
			$args	= "";
			for ($i = 0; $i < $n; $i++) {
				$args	.= $this->_r();
			}

			return $args;
		}


		/**
		 * Beautify array of arguments for display. Not technically needed but looks nice for unknowns
		 */
		protected function _prettyArgs($args, $format = "%02x") {
			$out	= array();
			foreach ($args as $arg) {
				$out[]	= sprintf($format, $arg);
			}
			return implode(", ", $out);
		}


		/**
		 * Read single binary byte from script data stream
		 */
		protected function _r() {
			if ($this->_ptr > $this->_len) {
				throw new \Exception("Pointer exceeded data length");
			}
			$o		= substr($this->_data, $this->_ptr, 1);
			$this->_ptr++;
			return $o;
		}


		/**
		 * Read a single unsigned integer from script data stream
		 */
		protected function _ri() {
			return ord($this->_r());
		}


	}
