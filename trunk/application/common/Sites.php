<?php

/**
 * Sitesl
 *  
 * @author Administrator
 * @version 
 */


class Sites extends Moore_Db_Table
{
	/**
	 * The default table name 
	 */
	protected $_name = 'sites';

	public function getSites()
	{
		$rows = $this->fetchAll();
		if($rows){
			$rows = $rows->toArray();
		}
		return $rows;
	}
	
	public function getSiteById( $site_id )
	{
		if(!$site_id){
			return false;
		}
		$row = $this->fetchRow("site_id = '$site_id'");
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}
	
	public function getSiteOptions()
	{
		$sites = $this->getSites();
		$options = array();
		foreach($sites as $s) {
			$options[$s['site_id']] = $s['site_name'];
		}
		return $options;
	}
}
