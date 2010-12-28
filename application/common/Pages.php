<?php

/**
 * pages table
 *
 */
class Pages extends Moore_Db_Table 
{
	protected $_name = 'pages';
	
	public function getAllQuery( $where=null,$order=null,$limit=null )
	{
		$query = $this->select()
		              ->from( $this->_name,array('*','id'=>'page_id'));
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

	public function getPageById( $id )
	{
		if( !$id ){
			return false;
		}
		$row = $this->fetchRow("page_id = '$id'");
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}
	
}