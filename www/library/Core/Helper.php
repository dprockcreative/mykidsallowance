<?php 

class Core_Helper {


	/**
	 *	Serialize Arguments
	 */
	public static function serializeArgs($args) {
		return ( ( is_object($args) || is_array($args) ) && count($args) > 0 ) ? serialize($args):'';
	}
	// END

	/**
	 *	Unserialize Arguments
	 */
	public static function unserializeArgs($str = '') {
		$args = @unserialize($str);
		return ( ( is_object($args) || is_array($args) ) && count($args) > 0 ) ? $args:array();
	}
	// END

	/**
	 *	Serialize Arguments
	 */
	public static function trimArgs($args = array()) {
		$temp = array();
		foreach($args as $key => $val) 
		{
			if( ! empty($val) ) 
			{
				$temp[$key] = $val;
			}
		}
		return $temp;
	}
	// END

	/**
	 *	Extract Data Object
	 *
	 *	@access		public
	 *	@author		dp
	 *	@edited		07.07.09
	 */
	public static function to_obj($query, $flatten = TRUE, $_lc_keys = FALSE, $override = FALSE) 
	{
		$array 	= (method_exists($query, 'toArray')) ? $query->toArray():$query;
 		$array 	= (isset($array['0']) && $flatten) ? $array['0']:$array;
		$obj 	= (object) array();

		if( count($array) == 0 ) 
		{
			return new stdClass;
		}

		foreach($array as $key => $val) 
		{
			if(is_array($val)) 
			{
				$k 			= ($_lc_keys) ? strtolower($key):$key;
				$val 		= ($override && count($val) == 1) ? self::setArray($val):$val;
				$obj->$k 	= self::to_obj($val, $flatten, $_lc_keys, $override);
			}
			else 
			{
				$k = ($_lc_keys) ? strtolower($key):$key;
				$obj->$k = $val;
			}
		}
		return $obj;
	}
	// END

	/**
	 *	OBJECT TO ARRAY
	 *
	 *	@access		public
	 *	@author		dp
	 *	@edited		07.07.09
	 */
	public function to_array($obj = NULL, $_lc_keys = TRUE) {
		if( is_null($obj) ) {
			return array();
		}
		$args = array();
		foreach($obj as $key => $val) {
			if(is_object($val)) {
				if($_lc_keys) {
					$k = strtolower($key);
				} 
				else {
					$k 		= $key;
					$tns 	= Support_Admin::getTableNames();
					if( array_key_exists($key, $tns) ) {
						$k = $tns[$key];
					}
				}
				$args[$k] = self::to_array($val, $_lc_keys);
			}
			else {
				$k = ($_lc_keys) ? strtolower($key):$key;
				$args[$k] = $val;
			}
		}
		return $args;
	}
	// END

	/**
	 *	Compile Global Data Object
	 *
	 * 	@author		dp
	 *	@date		05.15.09
	 *	@params		raw object
	 *	@return		compiled data object
	 *	@action		performs over-rides so sequence is important
	 */
	public function compile_data($data = null, $query, $name = NULL, $override = FALSE, $_lc_keys = FALSE) {

		if( empty($query) ) 
		{
			$data->$name = new stdClass;
			return $data;
		}

		$flatten 	= (is_null($name)) ? TRUE:FALSE;
		$obj 		= self::to_obj($query, $flatten, $_lc_keys, $override);

		if(is_object($obj))
		{
			foreach($obj as $key => $val) 
			{
				if($flatten) 
				{
					$data->$key = $val;
				}
				else 
				{
					if($override) 
					{ 
						$data->$name = $val;
					} 
					else 
					{
						$data->$name->$key = $val;
					}
				}
			}

			foreach($data as $key => $val) {
				if( ! is_string($val)) 
				{ 
					continue;
				}
				if(preg_match_all('/'.LD.'(.*?)'.RD.'/si', $val, $matches)) 
				{
					foreach($matches['0'] as $k => $match) 
					{
						$var = ($_lc_keys) ? strtolower($matches['1'][$k]):$matches['1'][$k];
						if(isset($data->$var)) 
						{
							$data->$key = str_replace($matches['0'][$k], $data->$var, $val);
						}
					}
				}
			}
		}

		return $data;
	}
	// END

	/**
	 *	User Created
	 */
	public static function userScreename($user_id = 1) {
		return Doctrine::getTable('Users')->find($user_id)->screenname;
	}
	// END

	/**
	 *	User Created
	 */
	public static function userCreated($user_id = 1, $date = null) {
		$date = date("M j, Y", strtotime($date));
		$name = Doctrine::getTable('Users')->find($user_id)->screenname;
		return "$date ($name)";
	}
	// END

	/**
	 *	User Updated
	 */
	public static function userUpdated($user_id = 1, $date = null) {
		$date = date("M j, Y", strtotime($date));
		$name = Doctrine::getTable('Users')->find($user_id)->screenname;
		return "$date ($name)";
	}
	// END

	/**
	 *	URI Filter
	 */
	public static function uri_filter($str = '') {

		$str = preg_replace('#[\&\-\@\#\$\%\^\*\(\)\+\=]\s+#', '', $str);

		$filters = new Zend_Filter();
		$filters
			->addFilter(new Zend_Filter_Alpha(true))
			->addFilter(new Zend_Filter_StringToLower(true))
			->addFilter(new Zend_Filter_Word_SeparatorToDash());

		return $filters->filter($str);
	}
	// END

	/**
	 *	Set URL String
	 */
	public static function setUrlString($str = '') {
		if( substr($str, -1, 1) != '/' ) {
			$str .= '/';
		}
		return $str;
	}
	// END
}
// END CLASS


// EOF