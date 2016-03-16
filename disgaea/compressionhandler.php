<?php

	namespace Disgaea;

	class CompressionHandler {

		protected	$_compressed	= "";
		protected	$_decompressed	= "";
		protected	$_cp			= 0;
		protected	$_dp			= 0;
		protected	$_volume		= 1;


		public function __construct($d, $l) {
			$this->_compressed		= $d;
			$this->_decompressed	= str_repeat("\x00", $l);
		}

		public function getDecompressedData() {
			return $this->_decompressed;
		}

		/**
		 * 0: Silent*
		 * 1: Normal
		 * 2: Describe events
		 * 3: Show actual byte values being written
		 */
		public function setLogLevel($volume) {
			$this->_volume	= $volume;
		}

		public function decompress() {

			$this->_cp	= 0;
			$this->_dp	= 0;

			$dataLen	= strlen($this->_compressed);

			$this->_log(1, sprintf("Decompressing %08X bytes of data...\n", $dataLen));
			$this->_log(1, sprintf("Expected size %08X\n", strlen($this->_decompressed)));

			while ($this->_cp < $dataLen) {

				$b	= $this->_cb();
				$this->_log(3, "\n");		// For separating out the byte values
				$this->_log(2, sprintf("\n%08X  ", $this->_cp));
				$logt	= sprintf("%08X", $this->_dp);


				if ($b == 0) {
					$this->_log(2, sprintf("Null-op??? %02X\n", $b));
					$this->_log(2, sprintf($logt ." Is this even allowed??"));
					$this->_cp++;
					continue;
				}

				// "If data[p] < $80, read and output data[p] bytes"
				if ($b > 0 && $b < 0x80) {
					$this->_log(2, sprintf("Direct copy: %02X\n", $b));
					$this->_log(2, sprintf($logt ."  Copying %02X bytes\n", $b));
					$this->_log(2, sprintf("          "));
					$this->_cp++;
					$this->_copy($b);
					continue;
				}

				// The ">= x && < y" is not needed but improves readability a bit

				// Turn the origin byte (optionally next 1 or 2) bytes into two numbers
				// indicating "bytes to copy" and "bytes to move back", respectively
				// removing the lower bound (e.g. 8F -> 0F, F2 = (F2 - E0) = 11)
				// 80: AB
				// C0: AA BB
				// E0: AA AB BB

				if ($b >= 0x80 && $b < 0xC0) {
					// One-byte lookbehind
					$this->_log(2, sprintf("1B lookbehind: %02X\n", $this->_cb()));
					$b	-= 0x80;
					$len	= (($b & 0xF0) >> 4) + 1;
					$back	= ($b & 0x0F) + 1;

				} elseif ($b >= 0xC0 && $b < 0xE0) {
					// Two bytes; first byte - C0, second byte normal
					$this->_cp++;
					$len	= $b - 0xC0 + 2;
					$back	= $this->_cb() + 1;
					$this->_log(2, sprintf("2B lookbehind: %02X %02X\n", $this->_cb(-1), $this->_cb()));

				} elseif ($b >= 0xE0 && $b <= 0xFF) {
					// Three bytes; AAA BBB for values
					$this->_cp++;
					$temp	= $this->_cb();
					$this->_cp++;
					$temp2	= $this->_cb();

					$len	= (($b - 0xE0) << 4) + (($temp & 0xF0) >> 4) + 3;
					$back	= (($temp & 0x0F) << 8) + $temp2 + 1;
					$this->_log(2, sprintf("3B lookbehind: %02X %02X %02X\n", $this->_cb(-2), $this->_cb(-1), $this->_cb()));

				} else {
					throw new Exception("This should never happen");
				}

				$this->_log(2, sprintf("$logt  Copying %03X from %03X bytes ago\n", $len, $back));
				$this->_copyback($len, $back);
				$this->_cp++;

			}

			$this->_log(1, "\n");
			return $this->_decompressed;

		}


		/**
		 * Copy $v bytes from source to decompressed version
		 */
		protected function _copy($num) {

			for ($i = $num; $i > 0; $i--) {
				$this->_log(3, sprintf("%02X", $this->_cb()));
				$this->_write_db($this->_cb());
				$this->_cp++;
			}

		}

		/**
		 * Copy $v bytes from source to decompressed version
		 */
		protected function _copyback($num, $back) {

			$back	*= -1;
			for ($i = $num; $i > 0; $i--) {
				$b	= $this->_db($back);
				$this->_log(3, sprintf("%02X", $b));
				$this->_write_db($b);
			}

		}



		protected function _getByte($from, $pos) {
			if ($pos > strlen($from) || $pos < 0) {
				throw new Exception("Asked to get byte at position $pos (outside range 0-". strlen($from) .")");
			}
			return ord($from{$pos});
		}

		protected function _cb($o = 0) {
			return $this->_getByte($this->_compressed, $this->_cp + $o);
		}

		protected function _db($o = 0) {
			return $this->_getByte($this->_decompressed, $this->_dp + $o);
		}

		/**
		 * Writes a byte to the decompressed output and increments the write pointer
		 */
		protected function _write_db($b, $o = 0) {
			$ro	= $this->_dp + $o;
			if ($ro < 0 || $ro > strlen($this->_decompressed)) {
				throw new Exception("Trying to write to byte outside of decompress area (ofs $ro)");
			}

			$this->_decompressed{$ro}	= chr($b);
			$this->_dp++;
		}


		protected function _log($vol, $msg) {

			if ($vol > $this->_volume) {
				return;
			}
			print $msg;

		}



		/**
		 * Hastily-written nop compression
		 * Rewrites data using nothing but "copy next values" bytes
		 * Does not actually compress anything
		 * Inflates file size by about 0.79%
		 */
		public static function compress($s, $size = false) {

			$len		= strlen($s);
			$chunksize	= ($size ? $size : 0x7F);

			if ($chunksize <= 0 || $chunksize >= 0x80) {
				throw new \Exception("'Compression' chunk size must be between 0x01-0x7F");
			}

			$out	= "";

			for ($p = 0; $p < $len; $p += $chunksize) {

				$sz		= min($chunksize, $len - $p);
				$out	.= chr($sz);
				$out	.= substr($s, $p, $chunksize);


			}

			return $out;

		}










	}
