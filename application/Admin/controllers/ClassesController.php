<?php

/**
 * AdminsController
 *
 * @author
 * @version
 */

class Admin_ClassesController extends Main_AdminController
{

	public function dataList()
	{
		$this->view->columns = array(

			array(
				'colName'  => 'class_name',
				'colType'  => 'text',
				'colTitle' => '欄目名稱'
			),

			array(
			    'colName'  => 'site_name',
			    'colType'  => 'text',
				'colTitle' => '所屬網站'
			)
		);
	}

	public function indexAction()
	{
		$this->view->navArray = array('title' => '欄目列表');
		$this->view->itemName = '欄目';
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
			$where = "c.class_name like '%$keyword%'";
		}
		if( $by && $order){
			$order_spec = array($by." ".$order);
		}
		else {
			$order_spec = array("class_id DESC");
		}
		$this->view->by = $by?$by:'class_id';
		$this->view->order = $order?$order:'asc';

		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$this->view->site_id = $site_id = $input->getEscaped('site')?$input->getEscaped('site'):1;
		$table = new Classes();
		$query = $table->getAllQuery( $site_id,$where,$order_spec);

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
		                              '欄目列表'=>$this->view->url(array('controller'=>'classes','module'=>'admin'),null,true),
		                              'title' => '添加新欄目'
		                             );
		$this->view->itemName = '欄目';
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
			$table = new Classes();
			$row = $table->getClassById( $id );
			$this->view->form = $form = new ClassEditForm( $site_id );
			$form->addSite( $site_id )
			     ->addParent( $site_id, $id );

			$form->setDefaults( $row );

			$this->view->navArray['title']= "修改欄目信息";

		}
		else{
			$this->view->form = $form = new ClassAddForm();
			$form->addSite( $site_id )
			     ->addParent( $site_id );
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

		$table = new Classes();
		if($id){
			$result = $table->update($data,"class_id = '$id'");
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
		                              '欄目列表'=>$this->view->url(array('controller'=>'classes','module'=>'admin'),null,true),
		                              'title' => '刪除欄目'
		                             );
		$this->view->itemName = '欄目';
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
		$table = new Classes();
		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->delete("class_id = '$id'");
				if($result){
					$this->_redirect('/admin/classes/index/site/'.$site_id);
				}
				else{
					$this->view->notice = "刪除失敗";
				}
			}
			else{
				$this->_redirect('/admin/classes/index/site/'.$site_id);
			}
		}
		$row = $table->getClassById( $id );
		$this->view->deleteName = $row['class_name'];
	}

}
