<?php

/**
 * EmailController
 *
 * @author
 * @version
 */


class Admin_EmailController extends Main_AdminController
{
    public function preDispatch()
    {
        $this->view->itemName = '郵件';
    }

	public function dataList()
	{
		$this->view->columns = array(

			array(
				'colName'  => 'email_title',
				'colType'  => 'text',
				'colTitle' => '標題'
			),
			array(
			    'colName'  => 'group_name',
			    'colType'  => 'text',
			    'colTitle' => '收件人'
			    
			)
			,
			array(
			   'colName'   => 'created_time',
			   'colType'   => 'text',
			   'colTitle'  => '發送時間'
			)

		);
	}

	public function indexAction()
	{
		$this->view->navArray = array('title' => '已發送郵件列表');
		
		$filter = new Filter();
		$input = $filter->filterValid( $this->_request->getParams() );
		if( !$input ){
			$this->view->error = "參數錯誤";
			return;
		}
		$this->view->keyword = $keyword = $input->getUnescaped('keyword');
		$order = $input->getEscaped('order');
		$by = $input->getEscaped('by');
		$currentPageNumber = $input->getEscaped('page')>0 ?$input->getEscaped('page'):1;
		$where = null;
		if($keyword){
			$where = "name like '%$keyword%'";
		}
		if( $by && $order){
			$order_spec = array($by." ".$order);
		}
		else {
			$order_spec = array("email_id DESC");
		}
		$this->view->by = $by?$by:'email_id';
		$this->view->order = $order?$order:'asc';

		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$table = new Emails();
		$query = $table->getAllQuery( $where,$order_spec);

		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($currentPageNumber)
				  ->setItemCountPerPage($pageSize)
        		  ->setPageRange($pageRange);

		$this->view->paginator = $paginator;
		$this->view->count = $paginator->getCurrentItemCount();
		$this->dataList();
	}

	public function editAction()
	{
		$this->view->navArray = array(
		                              '以發送郵件列表'=>$this->view->url(array('controller'=>'email','module'=>'admin'),null,true),
		                              'title' => '添加新 郵件'
		                             );

		// filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits'
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$id = $input->getEscaped('id');
		if($id){
			$table = new Emails();
			$row = $table->getEmailById( $id );
			$this->view->form = $form = new EmailEditForm();
			$form->setDefaults( $row );
			$this->view->navArray['title']= "查看已發送郵件";
		}
		else{
			$this->view->form = $form = new EmailAddForm();
			$form->setDefault('author',$this->loggedInUser->username);
		}

		if( !$this->_request->isPost() ){
			return;
		}
		if( !$form->isValid($this->_request->getPost()) ){
			$errors = $form->getMessages();
			foreach($errors as $key=>$err){
				$this->view->notice = $form->getElement($key)->getLabel()." 格式不對";
        		return;
			}
		}

		$data = parent::formatData( $form->getValues());

		$table = new Emails();

		if($id){
	/*		$result = $table->update($data,"email_id = '$id'");
			if( $result ){
				$this->view->notice = "修改成功";
				return;
			}
	*/	}
		else{
			$result = $table->insert($data);
		    if( $result ){
		        $table = new EmailSends();
		        $table->addEmails($result,$form->getValue('email_to'));
				$noticeSession = new Zend_Session_Namespace('notice');
		        $noticeSession->notice = "郵件發送訂單成功，系統將以160封/小時的速度發送";
				$this->_redirect("/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}");
				return;
			}
		}
	}

	public function deleteAction()
	{
		$this->view->navArray = array(
		                              '已發送郵件列表'=>$this->view->url(array('controller'=>'email','module'=>'admin'),null,true),
		                              'title' => '刪除郵件'
		                             );
		
		// filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits',
	    		'submit-y' => 'notEmpty',
	    		'submit-n' => 'notEmpty'
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$this->view->id = $id = $input->getEscaped('id');
		$table = new Emails();
		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->delete("email_id = '$id'");
				if($result){
					$this->_redirect('/admin/email');
				}
				else{
					$this->view->notice = "刪除失敗 ";
				}
			}
			else{
				$this->_redirect('/admin/email');
			}
		}
		$row = $table->getEmailById( $id );
		$this->view->deleteName = $row['email_title'];
	}

}
