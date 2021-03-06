<?php

/**
 * AllowanceConfigs
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package	##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author	 ##NAME##
 * @version	SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
class AllowanceConfigs extends BaseAllowanceConfigs {

	public function getBaseAmount($allowance_id) {
		try {
			$query = Doctrine_Query::create()
									->select('SUM(ac.amount) AS total')
									->from('AllowanceConfigs ac')
									->leftJoin('ac.Configurations c')
									->where('ac.allowance_id = ?', $allowance_id)
									->andWhere('c.type_id = ?', Core_Pay::BASE_TYPE_ID)
									->execute(array(), Doctrine::HYDRATE_ARRAY);
		} catch(Exception $e) {
			Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
		}
		return ( empty($query[0]['total']) ) ? '0.00':$query[0]['total'];
	}
	// END

	public function hasBase($allowance_id) {

		try {
			$query = Doctrine_Query::create()
									->select('ac.*')
									->from('AllowanceConfigs ac')
									->leftJoin('ac.Configurations c')
									->where('ac.allowance_id = ?', $allowance_id)
									->andWhere('ac.active = ?', '1')
									->andWhere('c.type_id = ?', Core_Pay::BASE_TYPE_ID)
									->execute(array(), Doctrine::HYDRATE_ARRAY);
		} catch(Exception $e) {
			Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
		}
		return ( empty($query) ) ? false:true;
	}
	// END

}
// END CLASS