<?php 

class Core_P {

	public function __construct($obj = NULL, $die = FALSE) {
		return $this->p($obj, $die);
	}

	public function p($obj = NULL, $die = FALSE) {
		if( is_string($obj) ) {
			echo 'string:: '.PHP_EOL.$obj.PHP_EOL.PHP_EOL;
		}
		elseif( is_int($obj) ) {
			echo 'integer:: '.PHP_EOL.$obj.PHP_EOL.PHP_EOL;
		}
		elseif( is_float($obj) ) {
			echo 'float:: '.PHP_EOL.$obj.PHP_EOL.PHP_EOL;
		}
		elseif( is_object($obj) || is_array($obj) ) {
			print_r($obj);
			echo PHP_EOL;
		}
		elseif( is_bool($obj) ) {
			echo 'bool:: '.PHP_EOL.(($obj == true) ? "true":"false").PHP_EOL.PHP_EOL;
			echo PHP_EOL;
		}
		else {
			echo PHP_EOL.'end'.PHP_EOL;
			die;
		}
		if($die) { die; } 
	}
}