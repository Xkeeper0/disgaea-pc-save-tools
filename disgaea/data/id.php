<?php

	namespace Disgaea\Data;

	class ID {
		// Hold IDs

		protected static	$_items			= array();
		protected static	$_innocents		= array();
		protected static	$_classes		= array();

		// Not creatable, go away
		protected function __construct() {}

		public static function init() {
			static::$_items		= static::_parse(static::_getFile("db/items.txt"));
			static::$_innocents	= static::_parse(static::_getFile("db/innocents.txt"));
			static::$_classes	= static::_parse(static::_getFile("db/classes.txt"));
		}


		public static function getInnocent($id) {
			if (isset(static::$_innocents[$id])) {
				return static::$_innocents[$id];
			} else {
				return sprintf("Jobless (%02x)", $id);
			}
		}


		public static function getItem($id) {
			if (isset(static::$_items[$id])) {
				return static::$_items[$id];
			} else {
				return sprintf("(None) (%02x)", $id);
			}
		}


		public static function getClass($id) {
			if (isset(static::$_classes[$id])) {
				return static::$_classes[$id];
			} else {
				return sprintf("(Unknown %02x)", $id);
			}
		}


		protected static function _parse($data) {

			$out	= array();
			$da		= explode("\n", $data);

			foreach ($da as $line) {

				if (!($line = trim($line)) || $line{0} == "#") {
					// Skip empty and comment lines
					continue;
				}

				$la	= explode("\t", $line);
				$out[hexdec($la[0])]	= trim($la[1]);

			}

			return $out;
		}



		protected static function _getFile($f) {
			return file_get_contents(__DIR__ ."/". $f);
		}
	}


	ID::init();