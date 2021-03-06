<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Users', 'default');

/**
 * BaseUsers
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $group_id
 * @property string $screenname
 * @property string $username
 * @property string $password
 * @property string $unique_id
 * @property timestamp $lastlogin
 * @property timestamp $created
 * @property integer $author_id
 * @property timestamp $updated
 * @property integer $editor_id
 * @property integer $active
 * @property Doctrine_Collection $Configurations
 * @property Doctrine_Collection $Allowances
 * @property Doctrine_Collection $Members
 * 
 * @package	##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author	 ##NAME## ##EMAIL##
 * @version	SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseUsers extends Doctrine_Record {

	public function setTableDefinition() {
		$this->setTableName('users');
		$this->hasColumn('id', 'integer', 4, array('type' => 'integer', 'length' => 4, 'fixed' => false, 'unsigned' => true, 'primary' => true, 'autoincrement' => true));
		$this->hasColumn('group_id', 'integer', 1, array('type' => 'integer', 'length' => 1, 'fixed' => false, 'unsigned' => true, 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('screenname', 'string', 32, array('type' => 'string', 'length' => 32, 'fixed' => false, 'unsigned' => false, 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('username', 'string', 16, array('type' => 'string', 'length' => 16, 'fixed' => false, 'unsigned' => false, 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('password', 'string', 32, array('type' => 'string', 'length' => 32, 'fixed' => false, 'unsigned' => false, 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('unique_id', 'string', 32, array('type' => 'string', 'length' => 32, 'fixed' => false, 'unsigned' => false, 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('lastlogin', 'timestamp', null, array('type' => 'timestamp', 'fixed' => false, 'unsigned' => false, 'primary' => false, 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('created', 'timestamp', null, array('type' => 'timestamp', 'fixed' => false, 'unsigned' => false, 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('author_id', 'integer', 4, array('type' => 'integer', 'length' => 4, 'fixed' => false, 'unsigned' => true, 'primary' => false, 'default' => '1', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('updated', 'timestamp', null, array('type' => 'timestamp', 'fixed' => false, 'unsigned' => false, 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('editor_id', 'integer', 4, array('type' => 'integer', 'length' => 4, 'fixed' => false, 'unsigned' => true, 'primary' => false, 'default' => '1', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('active', 'integer', 1, array('type' => 'integer', 'length' => 1, 'fixed' => false, 'unsigned' => true, 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
	}

	public function setUp()
	{
		parent::setUp();
		$this->hasMany('Configurations', array('local' => 'id', 'foreign' => 'user_id'));
		$this->hasMany('Allowances', array('local' => 'id', 'foreign' => 'user_id'));
		$this->hasOne('Members', array('local' => 'id', 'foreign' => 'id'));
	}
}