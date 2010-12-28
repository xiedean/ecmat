<?php

/**
 * albumns table
 *
 */
class Albumns extends Moore_Db_Table
{
	protected $_name = 'albumns';

	public function getAllQuery( $where=null,$order=null,$limit=null )
	{
		$query = $this->select()
		              ->from( $this->_name,array('*','id'=>'albumn_id'));
		$auth = Zend_Auth::getInstance();
		if( !($auth->hasIdentity()  && $auth->getIdentity()->role == "administrator")){
			$query->where("status = '1'");
		}
		if($where){
			$query->where($where);
		}
		if($order){
			$query->order($order);
		}
		else{
			$query->order('albumn_id DESC');
		}
		if($limit){
			$query->limit($limit);
		}
		return $query;
	}

	public function getAlbumn( $id, $str=null )
	{
		if( !$id ){
			return false;
		}
		$where = "albumn_id = '$id'";
		if($str){
		    $where = "albumn_id = '$id' and string = '$str'";
		}
		$row = $this->fetchRow($where);
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}

	public function deleteAlbumn ( $id )
	{
	    $result = $this->delete("albumn_id = '$id'");
	    if($result){
	        $table = new Photos();
	        $table->deleteAlbumn($id);
	        return true;
	    }
	    return false;
	}

}