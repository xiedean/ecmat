<?php

/**
 * Notices
 *
 * @author Administrator
 * @version
 */


class Notices extends Moore_Db_Table
{
	/**
	 * The default table name
	 */
	protected $_name = 'notices';

	public function getAllQuery( $where=null,$order=null,$limit=null )
	{
		$query = $this->select()
		              ->from( $this->_name,array('*','id'=>'notice_id'));
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

	public function getNoticeById( $id )
	{
		if( !$id ){
			return false;
		}
		$row = $this->fetchRow("notice_id = '$id'");
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}

	public function getTopNotices( $belong,$num=null )
	{
	    if( !$num ){
	        $num = 5;
	    }
	    $rows = $this->fetchAll("belong = '$belong' and status = 1",'notice_id DESC', $num);
	    if( count($rows) > 0){
	        return $rows->toArray();
	    }
	    else return false;
	}

}
