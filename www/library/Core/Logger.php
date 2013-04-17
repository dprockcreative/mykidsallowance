<?php

/**
 *	Core Exception Logger.
 *
 */
class Core_Logger {

	public static function setup(Zend_Config_Ini $config) {
		$filename = $config->log->filename;
		$logLevel = (int) $config->log->level;

		$writer = new Zend_Log_Writer_Stream($filename);
		$writer->addFilter(new Zend_Log_Filter_Priority($logLevel));

		$logger = new Zend_Log($writer);
		Zend_Registry::set('logger', $logger);
		date_default_timezone_set('America/Los_Angeles');
	}

	public static function getInstance() {
		return(Zend_Registry::get('logger'));
	}

	public static function debug($aString) {
	  Logger::getInstance()->debug($aString);  
	}

	public static function trace($aDeep = 1, $aArgDetail = false) {
		$output = "";
		$raw = debug_backtrace();	   
		foreach ($raw as $key => $entry) {
			if ($key == 0 ||
				($aDeep < 0 && $key == 1)) {
				continue;
			}
			if ($aDeep >= 0 && $key > $aDeep+1) {
				break;
			}
			$idx = $key-1;
			$output .= "\n#{$idx} ";
			if ($aArgDetail) {
				$arg = print_r($entry['args'], 1);
			} else {
				$arg = implode(', ', $entry['args']);
			}
			if (isset($entry['class'])) {
				$output .= "{$entry['class']}{$entry['type']}{$entry['function']}({$arg}) ";
			} else {
				$output .= "{$entry['function']}({$arg}) ";
				if (isset($entry['file'])) {
					$output .= "{$entry['file']}:{$entry['line']}";
				}
			}
		}
		self::debug($output);
	}

	public static function traceAll($aArgDetail = false) {
		self::trace(-1, $aArgDetail);
	}
}
