<?php

/**
 * Sessions
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package	##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author	 ##NAME## ##EMAIL##
 * @version	SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Sessions extends BaseSessions { 

	public function saveSession($session = array()) {
		try {
			$query = new Sessions();
			$query->fromArray($session);
			$query->save();
		} catch(Exception $e){
			Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
		}
	}
	// END

	public function closeSession($session_id) {
		try {
			$query = Doctrine::getTable('Sessions')->find($session_id);
			$query->fromArray(array('last_activity' => time()));
			$query->save();
		} catch(Exception $e){
			Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
		}
		return ( empty($query) ) ? 0:1;
	}
	// END

	public function killSession($session_id) {
		try {
			$delete = Doctrine::getTable('Sessions')->findBySessionId($session_id);
			$delete->delete();
		} catch(Exception $e){
			Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
		}
		return ( empty($delete) ) ? 0:1;
	}
	// END

}
// END CLASS