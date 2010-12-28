<?php

/**
 * admins table
 *
 */
class Admins extends Moore_Db_Table
{
	protected $_name = 'admins';

	public function getAllQuery( $where=null,$order=null,$limit=null )
	{
		$query = $this->select()
		              ->from( $this->_name,array('*','id'=>'user_id'));
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

	public function reLogin ($id)
	{
	    $where = $this->getAdapter()->quoteInto('user_id = ?', $id);
        $user_result = $this->fetchRow($where);
        $user_result->password = null;
        if ($user_result) {
            $auth = Zend_Auth::getInstance();
            $auth->getStorage()->write($user_result);
            return true;
        }
        return false;
	}
}