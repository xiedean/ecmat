<?php

class Moore_Mail
{
    protected $_email;
    protected $_name;

    public function __construct( )
    {
        $registry = Zend_Registry::getInstance();
        $this->_email = $registry->get('siteEmail');
        $this->_name = $registry->get('siteName');

    }

    public function setEmail($email)
    {
        $this->_email = $email;
    }

    public function setName( $name )
    {
        $this->_name = $name;
    }

	public function send($email, $title , $body, $from= null)
	{
		$mail = new Zend_Mail('utf-8');
		if($from) 
		    $mail->setFrom($from);
		else
		    $mail->setFrom($this->_email, $this->_name);
		$mail->setSubject($title);
		$mail->setBodyHtml($body);
		$mail->addTo($email);
		return $mail->send();
	}


}