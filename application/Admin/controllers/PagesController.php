<?php

/**
 * PagesController
 *
 * @author
 * @version
 */


class Admin_PagesController extends Main_AdminController
{

	public function dataList()
	{
		$this->view->columns = array(

			array(
				'colName'  => 'page_title',
				'colType'  => 'text',
				'colTitle' => '頁面名稱'
			)
		);
	}

	public function indexAction()
	{
		$this->view->navArray = array('title' => '頁面列表');
		$this->view->itemName = '頁面';
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
			$where = "page_title like '%$keyword%'";
		}
		if( $by && $order){
			$order_spec = array($by." ".$order);
		}
		else {
			$order_spec = array("page_id DESC");
		}
		$this->view->by = $by?$by:'page_id';
		$this->view->order = $order?$order:'asc';

		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$table = new Pages();
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
		                              '頁面列表'=>$this->view->url(array('controller'=>'pages','module'=>'admin'),null,true),
		                              'title' => '添加新頁面'
		                             );
		$this->view->itemName = '頁面';
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
			$table = new Pages();
			$row = $table->getPageById( $id );
			$this->view->form = $form = new PageEditForm();

			$form->setDefaults( $row );
			$this->view->navArray['title']= "修改頁面信息";
		}
		else{
			$this->view->form = $form = new PageAddForm();
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

		$table = new Pages();

		if($id){
			$result = $table->update($data,"page_id = '$id'");
			if( $result ){
				$noticeSession = new Zend_Session_Namespace('notice');
		        $noticeSession->notice = "修改成功";
				$this->_redirect("/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}");
				return;
			}
		}
		else{
			$result = $table->insert($data);
		    if( $result ){
				$this->view->notice = "添加成功";
				return;
			}
		}
	}

	public function deleteAction()
	{
		$this->view->navArray = array(
		                              '頁面列表'=>$this->view->url(array('controller'=>'pages','module'=>'admin'),null,true),
		                              'title' => '删除頁面'
		                             );
		$this->view->itemName = '頁面';
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
		$table = new Pages();
		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->delete("page_id = '$id'");
				if($result){
					$this->_redirect('/admin/pages');
				}
				else{
					$this->view->notice = "刪除失敗 ";
				}
			}
			else{
				$this->_redirect('/admin/pages');
			}
		}
		$row = $table->getPageById( $id );
		$this->view->deleteName = $row['page_title'];
	}

}
