<?php

/**
 * Classes
 *
 * @author Administrator
 * @version
 */



class Groups extends Moore_Db_Table
{
	/**
	 * The default table name
	 */
	protected $_name = 'groups';

	public function getGroups()
	{
	    $rows = $this->fetchAll();
	    if($rows){
	        return $rows->toArray();
	    }
	    return false;
	}
	
	public function getGroupsBySite( $site_id )
	{
		if( !$site_id ){
			return false;
		}
		$rows = $this->fetchAll("belong = '$site_id'");
		if($rows){
			$rows = $rows->toArray();
		}
		return $rows;
	}

    public function getAllQuery( $site_id=null,$where=null,$order=null,$limit=null )
	{
		$query = $this->_db->select()
		              ->from( array('c'=>$this->_name),array('*','id'=>'group_id'))
		              ->joinLeft( array('s'=>'sites'),'s.site_id = c.belong','*');
		if($site_id) 
		    $query->where( "c.belong = ?",$site_id);
		if($where){
			$query->where($where);
		}
		if($order){
			$query->order($order);
		}
		if($limit){
			$query->limit($limit);
		}

		return $query;
	}

	public function getGroupById( $id )
	{
		if( !$id ){
			return false;
		}

		$row = $this->fetchRow("group_id = '$id'");
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}

	public function getGroupName( $id )
	{
	    $row = $this->fetchRow("group_id = '$id'");
	    if($row){
	        return $row->group_name;
	    }
	    return false;
	}
	
	public function getSiteByGroupId($group_id)
	{
	    $where = $this->_db->quoteInto("group_id = ?",$group_id);
	    $row = $this->fetchRow($where);
	    if($row)
	        return $row->belong;
	    else
	        return false;
	}
}
