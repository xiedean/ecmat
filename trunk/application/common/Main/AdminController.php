<?php
class Main_AdminController extends Moore_Controller_Action
{
	public function commonDispatch()
	{
		Zend_Layout::startMvc(array('layoutPath'=>'./application/Admin/views/layouts/'));

		$params = $this->_request->getParams();

        //set web title
        $this->view->title = "";

        $this->view->controllerName = $this->_request->getControllerName();
        $this->view->moduleName = $this->_request->getModuleName();
        $this->view->actionName =$this->_request->getActionName();

        $this->view->currentUrl = $this->_request->getModuleName().'/'.$this->_request->getControllerName().'/'.$this->_request->getActionName();
        foreach ($params as $key => $param){
            if( !in_array($key,array('controller','module','action')) && $this->view->moduleName == "admin" ){
                $this->view->currentUrl .= "/$key/$param";
            }
        }

        $this->acl = new Acl();
        if ( ! $this->acl->isAllowed($this->role, $this->getRequest()->getModuleName(), $this->getRequest()->getControllerName()) ) {
            $referSession = new Zend_Session_Namespace('refer');
            $referSession->refer = $this->view->currentUrl;
        	$this->_redirect('/admin/auth');
        }

        //get notice info
        $noticeSession = new Zend_Session_Namespace('notice');
        if($noticeSession->notice){
            $this->view->notice = $noticeSession->notice;
            $noticeSession->__unset('notice');
        }
	}

	public function postDispatch()
	{
		$response = $this->getResponse();
        $response->insert('css', $this->view->render('cssadmin.phtml'));
        if ( $this->_request->getControllerName() != 'auth' ){
            $response->insert('controlpanel', $this->view->render('controlpanel.phtml'));
            $response->insert('top', $this->view->render('top.phtml'));
        }
        $response->insert('bottom', $this->view->render('bottom.phtml'));

        $this->innerPostDispatch();

	}

	public function innerPostDispatch()
	{

	}

    function formatData ( $data )
    {
        $array = array ();
        foreach ($data as $key => $d) {
            if ( !in_array( $key, array("password2","submit","myfile","news_image","news_video") ) ) {
            	if( $key == "password" ){
            		if(trim($d) != "")
            		    $array[$key] = md5($d);
            	}else{
            		$array[$key] = stripcslashes($d);
            	}
            }
        }
        return $array;
    }
}