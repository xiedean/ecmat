<?php

/**
 * AdminsController
 *
 * @author
 * @version
 */


class Admin_AdminsController extends Main_AdminController
{

	public function dataList()
	{
		$this->view->columns = array(

			array(
				'colName'  => 'username',
				'colType'  => 'text',
				'colTitle' => '管理員名'
			),

			array(
			    'colName'  => 'user_email',
			    'colType'  => 'text',
				'colTitle' => 'E-mail'
			),

			array(
			    'colName'  => 'role',
			    'colType'  => 'text',
				'colTitle' => '管理員類型'
			),

			array(
			    'colName'  => 'last_login_time',
			    'colType'  => 'text',
				'colTitle' => '賞賜登陸時間'
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
		$this->view->navArray = array('title' => '管理員列表');
		$this->view->itemName = '管理員';
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
			$where = "username like '%$keyword%'";
		}
		if( $by && $order){
			$order_spec = array($by." ".$order);
		}
		else {
			$order_spec = array("user_id DESC");
		}
		$this->view->by = $by?$by:'user_id';
		$this->view->order = $order?$order:'asc';

		$registry = Zend_Registry::getInstance();
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$table = new Admins();
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
		                              '管理員列表'=>$this->view->url(array('controller'=>'admins','module'=>'admin'),null,true),
		                              'title' => '添加新管理員'
		                             );
		$this->view->itemName = '管理員';
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
			$table = new Admins();
			$row = $table->getUserById( $id );
			$this->view->form = $form = new UserEditForm();
			$form->setRoles( array('administrator'=> 'Administrator', 'editor'=>'Editor'));
			$form->setDefaults( $row );
			$this->view->navArray['title']= "修改管理員信息";
		}
		else{
			$this->view->form = $form = new UserAddForm();
			$form->setRoles( array('administrator'=> 'Administrator', 'editor'=>'Editor'));
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
		if(md5($form->getValue('password')) != md5($form->getValue('password2'))){
			$this->view->notice = "兩次密碼不一樣";
			return;
		}

		$data = parent::formatData( $form->getValues());

		$table = new Admins();
		$exist = $table->checkUserExist($form->getValue('username'),$form->getValue('user_email'));
		if($id){
		    if( count($exist)>1 ) {
				$this->view->notice = "管理員名 或 郵箱 已經被使用";
				return;
			}
			$result = $table->update($data,"user_id = '$id'");
			if( $result ){
				$this->view->notice = "修改成功";
				return;
			}
		}
		else{
			if( count($exist) ){
				$this->view->notice = "管理員名 或 郵箱 已經被使用";
				return;
			}
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
		                              '管理員列表'=>$this->view->url(array('controller'=>'admins','module'=>'admin'),null,true),
		                              'title' => '刪除管理員'
		                             );
		$this->view->itemName = '管理員';
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
		$table = new Admins();
		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->delete("user_id = '$id'");
				if($result){
					$this->_redirect('/admin/admins');
				}
				else{
					$this->view->notice = "刪除失敗 ";
				}
			}
			else{
				$this->_redirect('/admin/admins');
			}
		}
		$row = $table->getUserById( $id );
		$this->view->deleteName = $row['username'];
	}

}
