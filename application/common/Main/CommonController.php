<?php
class Main_CommonController extends Moore_Controller_Action
{
	public function commonDispatch()
	{
	    Zend_Layout::startMvc(array('layoutPath'=>'./application/Default/views/layouts/'));
		$this->view->module = $moduleName = $this->_request->getModuleName();
		$this->view->controller = $countrollerName = $this->_request->getControllerName();
		$this->view->action = $actionName = $this->_request->getActionName();

        $this->view->targeturl = $this->view->baseUrl() . '/' . $moduleName . '/' . $countrollerName . '/' . $actionName;

        $this->acl = new Acl();
        if ( ! $this->acl->isAllowed($this->role, $moduleName, $countrollerName) ) {
            $string = '';
            $params = $this->_request->getParams();
            foreach ($params as $key => $p) {
                if ( ! in_array($key, array ( 'action', 'controller', 'module' )) ) {
                    $string = $key . "/" . $p . "/";
                }
            }
            $referSession = new Zend_Session_Namespace('refer');
            $referSession->refer = $params['module'] . "/" . $countrollerName . "/" . $actionName . "/" . $string;
            $this->_redirect('/account/login');
        }

        // check the site
        $siteSession = new Zend_Session_Namespace('site');
        if( !isset($siteSession->site) ){
            $this->view->site = $siteSession->site = 1;  // mpro

        }
        else{
            $this->view->site = $siteSession->site;

        }
        if(in_array($_SERVER['SERVER_NAME'],array("echyang.com","www.echyang.com")) ){
            $this->view->site = 2;
	    }
        //get login form
        if( in_array($this->role,array('guest','administrator')) ){
            $this->view->loginForm = new LoginForm();
        }
        else{
            $this->view->noLoginForm = 1;
        }

	    //get notice info
        $noticeSession = new Zend_Session_Namespace('notice');
        if($noticeSession->notice){
            $this->view->notice = $noticeSession->notice;
            $noticeSession->__unset('notice');
        }
	    if($noticeSession->notice2){
            $this->view->notice2 = $noticeSession->notice2;
            $noticeSession->__unset('notice2');
        }

	}

	public function postDispatch()
	{
        //get yang's article
        $articles = new Articles();
		$response = $this->getResponse();
	    // check the site
        	
        $this->view->title = "台灣傳播管理研究協會資訊網";
        // get links info
        $links = new Links();
        $this->view->links = $links->getLinks(1);
        //get Upcoming Events info
        $notices = new Notices();
        $this->view->notices = $notices->getTopNotices(1,5);
        //get Today Events info
        $this->view->todayEvents = $notices->getTopNotices(2,3);
        //get Tomorrow Events info
        $this->view->tomorrowEvents = $notices->getTopNotices(3,3);
        //get "产业点评"
        $rows = $articles->getArticlesByClass(36,1);
        if(count($rows)>0){
            $this->view->industryComment = $rows[0];
        }
        //get sub classes of "行业资讯"
        $classes = new Classes();
        $this->view->subClasses = $classes->getSubClasses(3);

        $response->insert('top',$this->view->render('top.phtml'));
        

        $response->insert('bottom',$this->view->render('bottom.phtml'));
        $response->insert('left',$this->view->render('left.phtml'));
        $response->insert('right',$this->view->render('right.phtml'));

        $this->innerPostDispatch();
		
		//update site visite
		if($this->_request->getControllerName() != "cron" && $this->_request->getModuleName() != "admin") {
			if(!isset($_COOKIE['site_visit'])) {
				setcookie('site_visit',1,time()+3600*24);
				$preferences = new Preferences();
				$preferences->updateVisit();
			}
		}
	}

	public function innerPostDispatch()
	{

	}

    function formatData ( $data )
    {
        $array = array ();
        foreach ($data as $key => $d) {
            if ( !in_array( $key, array("password2","submit",'captcha') ) ) {
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