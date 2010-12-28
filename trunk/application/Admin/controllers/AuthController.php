<?php
class Admin_AuthController extends Main_AdminController 
{
	/**
	 * init function will do first in all Action of this Controller
	 *
	 */
	function preDispatch()
	{
		
	}
	
	/**
	 * Admin login page
	 *
	 */
	function indexAction()
	{
		if( $this->role == 'administrator' ) {
			$this->_redirect('/admin');
		}
		$this->view->title = "管理員登陸";
		
        $this->view->form = $form = new LoginForm();

        if( !$this->_request->isPost() ){
        	return;
        }
        if( !$form->isValid($this->_request->getPost()) ){
        	$errors = $form->getMessages();
        	foreach($errors as $key=>$err){
        		$this->view->notice = $form->getElement($key)->getLabel()." 格式不對";
        		$this->view->errorId = $key;   //js focous id
        		return;
        	}
        }
			
		//setup Zend_Auth adapter for a database table
		$db = Zend_Registry::get ( 'dbAdapter' );
		$authAdapter = new Zend_Auth_Adapter_DbTable ( $db );
		
		$authAdapter->setTableName ( 'admins' );
		$authAdapter->setIdentityColumn ( 'username' );
		$authAdapter->setCredentialColumn ( 'password' );
		//set the column need to be authorized
		$authAdapter->setIdentity ( $form->getValue('username') );
		$authAdapter->setCredential ( md5($form->getValue('password')) );
		//authorize
		$auth = Zend_Auth::getInstance ();
		$result = $auth->authenticate ( $authAdapter );
		
		if ($result->isValid ()) {
			$data = $authAdapter->getResultRowObject ( null, 'password' );
			$auth->getStorage ()->write ( $data );
			$referSession = new Zend_Session_Namespace('refer');
			if( $referSession->refer){
				$refer = $referSession->refer;
				$referSession->unsetAll();
				$this->_redirect($refer);
				return;
			}
			$this->_redirect('/admin');
		} else {
			$this->view->notice = "用戶名或者密碼錯誤";
		}
        
	}
	
	/**
	 * admin logout page
	 *
	 */
	function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect('/admin/auth');
	}

}
