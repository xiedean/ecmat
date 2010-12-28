<?php 
class Session extends Moore_Db_Table
{
	protected  $_name = 'session';


	/**
	 * check if viewer online
	 *
	 * @param int $id: viewer id
	 * @param string $type: viewer type
	 * @return bool
	 */
	public function exist($id,$type = NULL)
	{
		if($type){
			$where = $this->getAdapter()->quoteInto('owner_id = ?',$id);
		}
		else {
			$where = $this->getAdapter()->quoteInto('viewer_id = ?',$id);
		}
		$result = $this->fetchRow($where);
		if($result) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * delete expired viewer
	 *
	 */
	public function  delexpire()
	{
		$curtime = time();
		$where = $this->getAdapter()->quoteInto('last_activity_time < ?',$curtime);
		$this->delete($where);
	}

	/**
	 * get time data
	 *
	 * @return array 
	 */
	public function getdata()
	{
		$curtime = time()+3600;
		$data = array('last_activity_time'=>$curtime
		);
		return $data;
	}

	/**
	 * update session table thought viewer_id and type
	 *
	 * @param int $id: viewer id
	 * @param string $type viewer type
	 * 
	 * @return int or false if not update.
	 */
	public function updateSession($id,$type = NULL)
	{
		$sid = session_id();
		if($type){
			$where = $this->getAdapter()->quoteInto('owner_id = ?',$id);
		}
		else {
			$where = $this->getAdapter()->quoteInto('viewer_id = ?',$id);
		}
		$data = $this->getdata();
		$result = $this->update($data,$where);
		return $result;
	}

    /**
     * insert a record
     *
     * @param int $viewer_id
     * @param int $owner_id
     * 
     * @return int or false if not insert
     */
	public function insertSession($viewer_id = NULL , $owner_id = NULL)
	{
		$session_id = session_id();
		$data = $this->getdata();
		$data['session_id'] = $session_id;
		$data['owner_id'] = $owner_id;
		$data['viewer_id'] = $viewer_id;
		$result = $this->insert($data);
		return $result;
	}
	
	/**
	 * get all the online member
	 *
	 * @return array
	 */
	public function getOnlineMembers( $pagesize=null, $start=null )
	{
		$query = $this->_db->select()
		                  ->from('session AS s')
		                  ->joinLeft('owners AS o','s.owner_id = o.owner_id','*')
		                  ->joinLeft('viewers AS v','s.viewer_id = v.viewer_id','*')
		                  ->where( 's.viewer_id is not null OR s.owner_id is not null')
		                  ->order('s.last_activity_time DESC');
		if($pagesize && $start) {
			$query->limit($pagesize, $start);
		}
		$result = $this->_db->fetchAll($query);
		return $result;
	}
	
}