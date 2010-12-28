<?php

/**
 * AdminsController
 *
 * @author
 * @version
 */

class Admin_GroupController extends Main_AdminController
{

	public function dataList()
	{
		$this->view->columns = array(

			array(
				'colName'  => 'group_name',
				'colType'  => 'text',
				'colTitle' => '分組名稱'
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
		$this->view->navArray = array('title' => '分組列表');
		$this->view->itemName = '分組';
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
			$where = "c.group_name like '%$keyword%'";
		}
		if( $by && $order){
			$order_spec = array($by." ".$order);
		}
		else {
			$order_spec = array("group_id DESC");
		}
		$this->view->by = $by?$by:'group_id';
		$this->view->order = $order?$order:'asc';

		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$this->view->site_id = $site_id = $input->getEscaped('site')?$input->getEscaped('site'):null;
		$table = new Groups();
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
		                              '分組列表'=>$this->view->url(array('controller'=>'group','module'=>'admin'),null,true),
		                              'title' => '添加新分組'
		                             );
		$this->view->itemName = '分組';
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
			$table = new Groups();
			$row = $table->getGroupById( $id );
			$this->view->form = $form = new GroupEditForm( $site_id );
			$form->setDefaults( $row );
			$this->view->navArray['title']= "修改分組信息";
		}
		else{
			$this->view->form = $form = new GroupAddForm();
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

		$table = new Groups();
		if($id){
			$result = $table->update($data,"group_id = '$id'");
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
		                              '分組列表'=>$this->view->url(array('controller'=>'group','module'=>'admin'),null,true),
		                              'title' => '刪除分組'
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
		$table = new Groups();
		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->delete("group_id = '$id'");
				if($result){
					$this->_redirect('/admin/group/index/site/'.$site_id);
				}
				else{
					$this->view->notice = "刪除失敗 ";
				}
			}
			else{
				$this->_redirect('/admin/group/index/site/'.$site_id);
			}
		}
		$row = $table->getGroupById( $id );
		$this->view->deleteName = $row['group_name'];
	}

}
