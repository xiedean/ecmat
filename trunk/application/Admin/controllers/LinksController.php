<?php

/**
 * NoticesController
 *
 * @author
 * @version
 */


class Admin_LinksController extends Main_AdminController
{

	public function dataList()
	{
		$this->view->columns = array(

			array(
				'colName'  => 'name',
				'colType'  => 'text',
				'colTitle' => '鏈接名稱'
			),
			array(
			    'colName'  => 'belong',
			    'colType'  => 'enum',
			    'colTitle' => '所屬網站',
			    'colValues'=> array('1'=>'MPRO','2'=>'Yang')
			)
			,
			array(
			   'colName'   => 'status',
			   'colType'   => 'enum',
			   'colTitle'  => '狀態',
			   'colValues' => array('0'=>'未激活','1'=>'激活')
			)

		);
	}

	public function indexAction()
	{
		$this->view->navArray = array('title' => '友情鏈接列表');
		$this->view->itemName = '友情鏈接';
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
			$order_spec = array("link_id DESC");
		}
		$this->view->by = $by?$by:'link_id';
		$this->view->order = $order?$order:'asc';

		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$table = new Links();
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
		                              '友情鏈接列表'=>$this->view->url(array('controller'=>'links','module'=>'admin'),null,true),
		                              'title' => '添加新友情鏈接'
		                             );
		$this->view->itemName = '友情鏈接';
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
			$table = new Links();
			$row = $table->getLinkById( $id );
			$this->view->form = $form = new LinkEditForm();

			$form->setDefaults( $row );
			$this->view->navArray['title']= "修改友情鏈接";
		}
		else{
			$this->view->form = $form = new LinkAddForm();
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

		$table = new Links();

		if($id){
			$result = $table->update($data,"link_id = '$id'");
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
				$this->_redirect("/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}");
				return;
			}
		}
	}

	public function deleteAction()
	{
		$this->view->navArray = array(
		                              '友情鏈接列表'=>$this->view->url(array('controller'=>'links','module'=>'admin'),null,true),
		                              'title' => '删除友情鏈接'
		                             );
		$this->view->itemName = '友情鏈接';
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
		$table = new Links();
		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->delete("link_id = '$id'");
				if($result){
					$this->_redirect('/admin/links');
				}
				else{
					$this->view->notice = "刪除失敗 ";
				}
			}
			else{
				$this->_redirect('/admin/links');
			}
		}
		$row = $table->getLinkById( $id );
		$this->view->deleteName = $row['name'];
	}

}
