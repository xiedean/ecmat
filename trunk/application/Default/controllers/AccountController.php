<?php
class AccountController extends Main_CommonController
{
    public function indexAction()
    {

    }

    public function loginAction()
    {
        $this->view->form = $form = new LoginForm();
        $this->view->headerName = "用户登录";
        $this->view->noLoginForm = 1; // don't show login form in right side

        if( !$this->_request->isPost())  {
          //  $this->_redirect('/');
            return;
        }

        if( !$form->isValid($this->_request->getPost()) ){
        	$errors = $form->getMessages();
        	foreach($errors as $key=>$err){
        		$this->view->notice = $form->getElement($key)->getLabel()." 格式不对";
        		$this->view->errorId = $key;   //js focous id
        		return;
        	}
        }

		//setup Zend_Auth adapter for a database table
		$db = Zend_Registry::get ( 'dbAdapter' );
		$authAdapter = new Zend_Auth_Adapter_DbTable ( $db );

		$authAdapter->setTableName ( 'users' );
        if($this->view->site == 2){
	        $authAdapter->setTableName ( 'users_yang' );
	    }
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
			$this->_redirect('/');
		} else {
			$this->view->notice = "用户名或者密码错误";
		}

    }


    public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect('/');
	}
}