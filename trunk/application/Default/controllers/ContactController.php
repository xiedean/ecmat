<?php
class ContactController extends Main_CommonController
{
	/**
	 * partner page
	 *
	 */
	function indexAction()
	{
		if($this->view->site == 1){
		    $this->view->headerName = "联系我们";
		}
		else{
		    $this->view->headerName = "联系杨志弘";
		}
		$this->view->selectItemId = "idsContact";
		$this->view->form = $form = new ContactForm();
		$table = new Pages();
		$this->view->row = $table->getPageById(1);

		if( !$this->_request->isPost() ){
		    return;
		}
		if( !$form->isValid( $this->_request->getPost()) ){
		    $errors = $form->getMessages();
			foreach($errors as $key=>$err){
				$this->view->notice = $form->getElement($key)->getLabel()." 格式不对";
				$this->view->errorId = $key;
				return;
			}
		}
		$registry = Zend_Registry::getInstance();
		$mail = new Moore_Mail();
		$mail->setEmail($form->getValue('email'));
		$mail->setName($form->getValue('name'));

		if($this->view->site == 1){
		   $to = $registry->get('siteEmail');
		}
		else{
		   $to = $registry->get('yangEmail');
		}
		

		$result = $mail->send($to,$form->getValue('title'),$form->getValue('content'));
		if( $result ){
		    $this->_redirect('/contact/succeed');
		}

	}
	public function succeedAction()
	{
	    header("refresh:5;url='{$this->view->baseUrl()}/index'");

	}

	function yangAction ()
	{
	     //get articles
	    $table = new Articles();
	    $this->view->articles1 = $table->getArticlesByClass( 4, 5 );   //class_id 4 meams "活动讯息"
	    $this->view->imageNews = $table->getTopImageNews( 4 );
	    $registry = Zend_Registry::getInstance();
	    $this->view->imagePath = $registry->get('newsImagePath');
	}

}