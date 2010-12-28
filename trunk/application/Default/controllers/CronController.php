<?php

/**
 * CronController
 *
 * @author
 * @version
 */


class CronController extends Main_CommonController
{
	public function indexAction()
	{
		$table = new EmailSends();
		$rows = $table->getEmails(3);
		if(!$rows) 
		    exit();
		$users = new Users();
		$usersYang = new UsersYang();
		$emails = new Emails();
		$emailAp = new Moore_Mail();
		foreach($rows as $key=>$r) {
		    $emailArr = $emails->getEmailById($r['email_id']);
		    if($emailArr){
		        if($r['site_id'] == 1) {
		            $userArr = $users->getUserById($r['user_id']);
		        }
		        else {
		            $userArr = $usersYang->getUserById($r['user_id']);
		        }
		        if($userArr) {
		            $title = str_replace('{$username}',$userArr['username'],$emailArr['email_title']);
		            $body = str_replace('{$username}',$userArr['username'],$emailArr['email_body']);
		            $from = $emailArr['email_from'];
		            $to = $userArr['user_email'];
		            $emailAp->send($to,$title,$body,$from);
		            $table->delete("email_send_id = '{$r['email_send_id']}'");
		        }
		    }
		}
		exit();
	}

	
}
