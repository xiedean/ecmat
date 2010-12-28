<?php

class UsersYang extends Moore_Db_Table
{
	/**
	 * The default table name
	 */
	protected $_name = 'users_yang';

	public function getAllQuery( $where=null,$order=null,$limit=null )
	{
		$query = $this->_db->select()
		              ->from( array('u'=>$this->_name),array('*','id'=>'user_id'))
		              ->joinLeft(array('g'=>'groups'),'g.group_id = u.group',array('group_name'));
		if($where){
			$query->where($where);
		}
		if($order){
			$query->order($order);
		}
		else{
			$query->order('user_id DESC');
		}
		if($limit){
			$query->limit($limit);
		}
		return $query;
	}

	public function getUserById( $id )
	{
		if( !$id ){
			return false;
		}
		$row = $this->fetchRow("user_id = '$id'");
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}

	public function getUserByEmail($email)
	{
	    if( !$email ){
	        return false;
	    }
	    $row = $this->fetchRow("user_email = '$email'");
	    if($row){
			$row = $row->toArray();
		}
		return $row;
	}

	public function checkUserExist( $username=null, $email=null)
	{
		$where = null;
		if($username){
			$where = "username = '$username'";
		}
		if($email){
			if($where){
				$where .= "AND user_email ='$email'";
			}
			else {
				$where = "user_email = '$email'";
			}
		}
		$row = $this->fetchAll( $where );
		return $row;
	}

	public function updateActivation( $activation )
	{
	    $data = array('status'=>1,'activation'=>'');
	    $id = $this->update($data,"activation = '$activation'");
	    return $id;
	}

	public function setPassword( $email,$password )
	{
	    $data = array('password'=>$password);
	    $id = $this->update($data,"user_email = '$email'");
	    return $id;
	}
	

	public function updateGroup($group, $where) 
	{
	    $data = array('group'=> $group);
	    $id = $this->update($data, $where);
	    return $id;
	}
	

	public function getUsersByGroup($group_id)
	{
	    $where = $this->_db->quoteInto("group = ?",$group_id);
	    $rows = $this->fetchAll($where);
	    if($rows->count()) 
	        return $rows->toArray();
	    else 
	        return false;
	}


	public function getBirthdayUsers($days)
	{
	    $daysLeft = 30 - $days;
	    $where = "(MONTH(birthday) - MONTH(NOW()) =0 AND DAY(birthday) - DAY(NOW()) <$days) OR ( MONTH(birthday) - MONTH(NOW()) > 0 AND  MONTH(birthday) - MONTH(NOW()) <= 1 AND  DAY(NOW()) - DAY(birthday) >$daysLeft )";
	    $rows = $this->fetchAll($where,"rand()");
	    if($rows->count()) 
	        return $rows->toArray();
	    else
	        return false;
	}
}
