<?php
class Acl  extends Zend_Acl
{

	public function Acl()
	{
		$this->add(new Zend_Acl_Resource('default'));   // resource as modules
        $this->add(new Zend_Acl_Resource('admin'));


        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('user'), 'guest');
        $this->addRole(new Zend_Acl_Role('administrator'),'guest');
        $this->addRole(new Zend_Acl_Role('editor'),'administrator');

        /* Guest */
        $this->allow('guest', 'default');
        $this->deny('guest', 'default', array('myaccount'));
        $this->allow('guest', 'admin', array('auth','albumns'));


        /* user */
        $this->allow('user', 'default');

        /* administrator */
        $this->allow('administrator');

        /*editor */
        $this->deny('editor','admin',array('admins'));


	}

}