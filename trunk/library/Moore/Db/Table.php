<?php

class Moore_Db_Table extends Zend_Db_Table {	
	/**
	 * Add a database table prefix
	 */
	function __construct() {
		$dbTbPrefix = Zend_Registry::get('dbTbPrefix');
		$this->_name = $dbTbPrefix.$this->_name;
		parent::__construct();
	}
}