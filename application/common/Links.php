<?php

class Links extends Moore_Db_Table
{
	/**
	 * The default table name
	 */
	protected $_name = 'links';

	public function getAllQuery( $where=null,$order=null,$limit=null )
	{
		$query = $this->select()
		              ->from( $this->_name,array('*','id'=>'link_id'));
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

	public function getLinkById( $id )
	{
		if( !$id ){
			return false;
		}
		$row = $this->fetchRow("link_id = '$id'");
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}

	public function getLinks($site)
	{
	    $rows = $this->fetchAll("belong = '$site'");

	    if(count($rows)>0){
	        return $rows->toArray();
	    }
	    else{
	        return false;
	    }
	}


}