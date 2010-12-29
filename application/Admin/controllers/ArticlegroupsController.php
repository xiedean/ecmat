<?php

/**
 * AdminsController
 *
 * @author
 * @version
 */

class Admin_ArticlegroupsController extends Main_AdminController
{

	public function dataList()
	{
		$this->view->columns = array(

			array(
				'colName'  => 'article_group_name',
				'colType'  => 'text',
				'colTitle' => '文章組'
			)

		);
	}

	public function indexAction()
	{
		$this->view->navArray = array('title' => '文章組列表');
		$this->view->itemName = '文章組';
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
			$where = "article_group_name like '%$keyword%'";
		}
		if( $by && $order){
			$order_spec = array("CONVERT( $by USING gbk ) ".$order);
		}
		else {
			$order_spec = array("article_group_id DESC");
		}
		$this->view->by = $by?$by:'article_group_id';
		$this->view->order = $order?$order:'asc';

		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$table = new ArticleGroups();
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
		                              '文章組列表'=>$this->view->url(array('controller'=>'articlegroups','module'=>'admin'),null,true),
		                              'title' => '添加新文章組'
		                             );
		$this->view->itemName = '文章組';
		// filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits',
		        'site'     => 'Digits'
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$id = $input->getEscaped('id');
		$this->view->site_id = $site_id = $input->getEscaped('site')?$input->getEscaped('site'):1;
		if($id){
			$table = new ArticleGroups();
			$row = $table->load( $id );
			$this->view->form = $form = new ArticleGroupEditForm( $site_id );
			$form->setDefaults( $row );
			$this->view->navArray['title']= "修改文章組信息";
		}
		else{
			$this->view->form = $form = new ArticleGroupAddForm();
		}

		if( !$this->_request->isPost() ){
			return;
		}

		if( !$form->isValid($this->_request->getPost()) ){
			$errors = $form->getMessages();
			foreach($errors as $key=>$err){
				$this->view->notice = $form->getElement($key)->getLabel()." 格式不對";
				$this->view->errorId = $key;
        		return;
			}
		}

		$data = parent::formatData( $form->getValues());

		$table = new ArticleGroups();
		if($id){
			$result = $table->update($data,"article_group_id = '$id'");
			if( $result ){
				$this->view->notice = "修改成功";
				return;
			}
		}
		else{
			$result = $table->insert($data);
		    if( $result ){
				$noticeSession = new Zend_Session_Namespace('notice');
		        $noticeSession->notice = "添加成功";
				$this->_redirect("/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}/index/site/$site_id");
				return;
			}
		}
	}

	public function deleteAction()
	{
		$this->view->navArray = array(
		                              '文章組列表'=>$this->view->url(array('controller'=>'articlegroups','module'=>'admin'),null,true),
		                              'title' => '刪除文章組'
		                             );
		$this->view->itemName = '文章組';
		// filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits',
	    		'submit-y' => 'notEmpty',
	    		'submit-n' => 'notEmpty',
		        'site'     => 'Digits'
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$this->view->site_id = $site_id = $input->getEscaped('site')?$input->getEscaped('site'):1;
		$this->view->id = $id = $input->getEscaped('id');
		$table = new ArticleGroups();
		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->delete("article_group_id = '$id'");
				if($result){
					$this->_redirect('/admin/articlegroups/index/site/'.$site_id);
				}
				else{
					$this->view->notice = "刪除失敗";
				}
			}
			else{
				$this->_redirect('/admin/articlegroups/index/site/'.$site_id);
			}
		}
		$row = $table->load( $id );
		$this->view->deleteName = $row['article_group_name'];
	}

}
