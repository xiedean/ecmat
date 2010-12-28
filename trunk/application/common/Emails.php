<?php

/**
 * Emails
 *
 * @author Administrator
 * @version
 */


class Emails extends Moore_Db_Table
{
	/**
	 * The default table name
	 */
	protected $_name = 'emails';

    public function getAllQuery( $where=null,$order=null,$limit=null )
	{
		$query = $this->_db->select()
		              ->from( array('e'=>$this->_name),array('*','id'=>'email_id'))
		              ->joinLeft(array('g'=>'groups'),"g.group_id = e.email_to");
		if($where){
			$query->where($where);
		}
		if($order){
			$query->order($order);
		}
		else{
		    $query->order("e.email_id DESC");
		}
		if($limit){
			$query->limit($limit);
		}

		return $query;
	}


	public function getEmailById( $id )
	{
		if( !$id ){
			return false;
		}

		$row = $this->fetchRow("email_id = '$id'");
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}

	
}
