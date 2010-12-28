<?php
class YangController extends Main_CommonController
{
    public function preDispatch()
    {
        $this->view->selectItemId = "id1";  // seletct tab item
        $siteSession = new Zend_Session_Namespace('site');
        $this->view->site = $siteSession->site = 2;
	}

    function indexAction()
	{
	    //get articles
	    $table = new Articles();
	    $this->view->articles1 = $table->getArticlesByClass( 4, 10 );   //class_id 4 meams "活动讯息"
	    $this->view->imageNews = $table->getTopImageNews( 4 );
	    $registry = Zend_Registry::getInstance();
	    $this->view->imagePath = $registry->get('newsImagePath');

	}


}