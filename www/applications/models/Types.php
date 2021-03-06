<?php

/**
 * Types
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package	##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author	 ##NAME## ##EMAIL##
 * @version	SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Types extends BaseTypes {

	public function getTypeOptions() {

		try {

			$query = Doctrine_Query::create()
									->select('t.id, t.type AS label')
									->from('Types t')
									->execute(array(), Doctrine::HYDRATE_ARRAY);

			$cp = new Core_Pay();

			if( count($query) > 0 ) {
				$options = array();
				foreach($query as $row) {
					$s = ( empty($cp->types[$row['id']]) ) ? '(-)':'(+)';
					$options[$row['id']] = $row['label']." ".$s;
				}
			}
		} catch(Exception $e) {
			Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
		}
		return ( empty($options) ) ? array():$options;
	}
	// END


}