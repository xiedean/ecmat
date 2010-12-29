<?php

/**
 * Classes
 *
 * @author Administrator
 * @version
 */



class Classes extends Moore_Db_Table
{
	/**
	 * The default table name
	 */
	protected $_name = 'classes';

	public function getClassesBySite( $site_id )
	{
		if( !$site_id ){
			return false;
		}
		$rows = $this->fetchAll("belong = '$site_id'","convert(class_name using gbk)");
		if($rows){
			$rows = $rows->toArray();
		}
		return $rows;
	}

    public function getAllQuery( $site_id,$where=null,$order=null,$limit=null )
	{
		$query = $this->_db->select()
		              ->from( array('c'=>$this->_name),array('*','id'=>'class_id'))
		              ->joinLeft( array('s'=>'sites'),'s.site_id = c.belong','*')
		              ->where( "c.belong = ?",$site_id);
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

	public function getClassById( $id )
	{
		if( !$id ){
			return false;
		}

		$row = $this->fetchRow("class_id = '$id'");
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}


	public function getMainClassesBySite ($site_id)
	{
	    if( !$site_id ){
	        return false;
	    }
	    $rows = $this->fetchAll("parent='0' and belong='$site_id' and status='1'");
	    if(count($rows) == 0){
	        return false;
	    }
	    return $rows->toArray();
	}

	public function getSubClasses( $id )
	{
	    $rows = $this->fetchAll("parent = '$id'");
	    if($rows->count()>0){
	        $rows = $rows->toArray();
	        return $rows;
	    }
	    return false;
	}

	public function getClassName( $id )
	{
	    $row = $this->fetchRow("class_id = '$id'");
	    if($row){
	        return $row->class_name;
	    }
	    return false;
	}
}
