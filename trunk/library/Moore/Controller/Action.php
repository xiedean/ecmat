<?php
class Moore_Controller_Action extends Zend_Controller_Action
{
	protected $loggedInUser = null;
	protected $role = 'guest';
	protected $acl = null;
	function init()
	{
		$module = $this->_request->getModuleName();

		if (isset($module) && !empty($module)) {
		//	if ($module != 'default')
			$module = ucfirst($module);
			$module .= '/';
		}
		set_include_path('./application/' . $module . 'models'
						. PATH_SEPARATOR . get_include_path());

		// get the authorization info and save to a class variable and a view variable loggedInUser
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$this->loggedInUser = $auth->getIdentity();
			$this->view->loggedInUser = $this->loggedInUser;
			$this->role = $this->view->role = $this->loggedInUser->role;
		} else {
			$this->view->loggedInUser = null;
		}

	//	$this->view->addBasePath('./application/views/');

	    
		$this->commonDispatch();
	}

	public function commonDispatch()
	{

	}


}