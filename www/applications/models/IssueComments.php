<?php

/**
 * IssueComments
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## 
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
class IssueComments extends BaseIssueComments {

	public function getLastByIssueId($issue_id) {

		try {
			$query = Doctrine::getTable('IssueComments')->findByIssueId($issue_id)->end()->toArray();

		} catch(Exception $e) {
			Core_Logger::getInstance()->err(__METHOD__ . " - " . $e);
		}
		return ( empty($query) ) ? null:$query;

	}
	// END
}
// END CLASS