<?php

/**
 * EmailSends
 *
 * @author Administrator
 * @version
 */


class EmailSends extends Moore_Db_Table
{
	/**
	 * The default table name
	 */
	protected $_name = 'email_sends';

	public function getEmails( $num=null )
	{
	    if(!$num)
	        $num = 3;
		$rows = $this->fetchAll(null,"email_send_id",$num);
		if($rows->count()){
			return $rows->toArray();
		}
		return false;
	}
	
	public function addEmails($email_id,$group_id)
	{
	    $table = new Groups();
	    $site_id = $table->getSiteByGroupId($group_id);
	    if(!$site_id) {
	        return false;
	    }
	    if($site_id == 1) {
	        $table = new Users();
	    }
	    if($site_id == 2) {
	        $table = new UsersYang();
	    }
	    $rows = $table->getUsersByGroup($group_id);
	    if(!$rows) 
	        return false;
	    foreach($rows as $r) {
	        $data = array('email_id'       => $email_id,
	                      'user_id'  => $r['user_id'],
	                      'site_id'        => $site_id
	        );
	        $result = $this->insert($data);
	    }
	    return $result;
	    
	    
	}

	
}
