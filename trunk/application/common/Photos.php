<?php

/**
 * photos table
 *
 */
class Photos extends Moore_Db_Table
{
	protected $_name = 'photos';

	public function getAllQuery( $where=null,$order=null,$limit=null )
	{
		$query = $this->select()
		              ->from( $this->_name,array('*','id'=>'photo_id'));
		if($where){
			$query->where($where);
		}
		if($order){
			$query->order($order);
		}
		else{
			$query->order('photo_id DESC');
		}
		if($limit){
			$query->limit($limit);
		}
		return $query;
	}

	public function getPhoto( $id, $str=null )
	{
		if( !$id ){
			return false;
		}
		$where = "photo_id='$id'";
		if($str){
		    $where = "photo_id = '$id' and string ='$str'";
		}
		$row = $this->fetchRow($where);
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}

	public function getPhotoOfAlbumn( $id )
	{
	    $where = "albumn_id = '$id'";
	    $row = $this->fetchRow($where);
	    if($row){
	        return $row->string;
	    }
	    return false;
	}

	public function deleteAlbumn ($id)
	{
	    $where = "albumn_id = '$id'";
	    $rows = $this->fetchAll($where);
	    if($rows->count()>0){
	        $registry = Zend_Registry::getInstance();
	        $path = $registry->get("rootPath").$registry->get("albumnPath");
	        foreach ($rows as $row){
	            if(is_file($path.$id."/".$row['string'])){
	                unlink($path.$id."/".$row['string']);
	            }
	        }
	        rmdir($path.$id);
	        $this->delete($where);
	        return true;
	    }
	    return false;
	}

	public function deletePhoto($id)
	{
	    $where = "photo_id = '$id'";
	    $row = $this->fetchRow($where);
	    if($row){
	        $registry = Zend_Registry::getInstance();
	        $path = $registry->get("rootPath").$registry->get("albumnPath");
	        if(is_file($path.$row['albumn_id']."/".$row['string'])){
	            unlink($path.$row['albumn_id']."/".$row['string']);
	        }
	        $this->delete($where);
	        return true;
	    }
	    return fasle;
	}

	public function getPrePhoto($id)
	{
	    $where = "photo_id = '$id'";
	    $a = $this->fetchRow($where);

	    $where = "photo_id > '$id' and albumn_id ='$a->albumn_id'";
	    $row = $this->fetchRow($where);
	    if($row){
	        $row = $row->toArray();
	    }
	    return $row;
	}

	public function getNextPhoto($id)
	{
		$where = "photo_id = '$id'";
	    $a = $this->fetchRow($where);
	    $where = "photo_id < '$id' and albumn_id ='$a->albumn_id'";
	    $order = "photo_id DESC";
	    $row = $this->fetchRow($where,$order);
	    if($row){
	        $row = $row->toArray();
	    }
	    return $row;
	}
}