<?php

/**
 * NoticesController
 *
 * @author
 * @version
 */


class Admin_NoticesController extends Main_AdminController
{

	public function dataList()
	{
		$this->view->columns = array(

			array(
				'colName'  => 'title',
				'colType'  => 'text',
				'colTitle' => '標題'
			),
			array(
				'colName'  => 'author',
				'colType'  => 'text',
				'colTitle' => '發布者'
			),
			array(
				'colName'  => 'created',
				'colType'  => 'text',
				'colTitle' => '發佈時間'
			),
			array(
			   'colName'   => 'belong',
			   'colType'   => 'enum',
			   'colTitle'  => '所屬欄目',
			   'colValues' => array('1'=>'活動公告','2'=>'今日看板','3'=>'明日活動')
			),
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
		$this->view->navArray = array('title' => '公告列表');
		$this->view->itemName = '公告';
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
			$where = "title like '%$keyword%'";
		}
		if( $by && $order){
			$order_spec = array($by." ".$order);
		}
		else {
			$order_spec = array("notice_id DESC");
		}
		$this->view->by = $by?$by:'notice_id';
		$this->view->order = $order?$order:'asc';

		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$table = new Notices();
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
		                              '公告列表'=>$this->view->url(array('controller'=>'notices','module'=>'admin'),null,true),
		                              'title' => '添加新公告'
		                             );
		$this->view->itemName = '公告';
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
			$table = new Notices();
			$row = $table->getNoticeById( $id );
			$this->view->form = $form = new NoticeEditForm();

			$form->setDefaults( $row );
			$this->view->navArray['title']= "修改公告信息";
		}
		else{
			$this->view->form = $form = new NoticeAddForm();
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

		$table = new Notices();

		if($id){
			$result = $table->update($data,"notice_id = '$id'");
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
		                              '公告列表'=>$this->view->url(array('controller'=>'notices','module'=>'admin'),null,true),
		                              'title' => '刪除公告'
		                             );
		$this->view->itemName = '公告';
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
		$table = new Notices();
		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->delete("notice_id = '$id'");
				if($result){
					$this->_redirect('/admin/notices');
				}
				else{
					$this->view->notice = "刪除失败 ";
				}
			}
			else{
				$this->_redirect('/admin/notices');
			}
		}
		$row = $table->getNoticeById( $id );
		$this->view->deleteName = $row['title'];
	}

}
