<?php

/**
 * Files
 *
 * @author Administrator
 * @version
 */



class Files extends Moore_Db_Table
{
	/**
	 * The default table name
	 */
	protected $_name = 'files';

	public function getAllQuery( $where=null,$order=null,$limit=null )
	{
		$query = $this->_db->select()
		              ->from( array('f'=>$this->_name),array('*','id'=>'file_id'))
		              ->joinLeft(array('s'=>'sites'),'f.class = s.site_id','*');
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

	public function getFileById( $id )
	{
		if( !$id ){
			return false;
		}
		$row = $this->fetchRow("file_id = '$id'");
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}

	public function checkFileName( $fileName )
	{
	    if(!$fileName){
	        return false;
	    }
	    $row = $this->fetchRow("file_name = '$fileName'");
	    if($row){
	        return true;
	    }
	    else{
	        return false;
	    }
	}

	public function getFilesBySite( $site_id )
	{
	    if(!$site_id){
	        return false;
	    }
	    $rows = $this->fetchAll("class = '$site_id' AND status = '1'");
	    if(count($rows)>0){
	        return $rows->toArray();
	    }
	    else{
	        return false;
	    }
	}

	public function getFileByName( $name )
	{

		$row = $this->fetchRow("file_name_src = '$name'");
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}

}
