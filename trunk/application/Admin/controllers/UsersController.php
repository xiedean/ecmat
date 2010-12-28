<?php
class Admin_UsersController extends Main_AdminController
{

	public function dataList()
	{
		$this->view->columns = array(
		    array(
		        'colType' => 'checkAllColumn'
		    ),
		    
			array(
				'colName'  => 'username',
				'colType'  => 'text',
				'colTitle' => '用戶名'
			),

			array(
			    'colName'  => 'user_email',
			    'colType'  => 'text',
				'colTitle' => 'E-mail'
			),

			array(
			    'colName'  => 'role',
			    'colType'  => 'text',
				'colTitle' => '用戶類型'
			),

			array(
			    'colName'  => 'group_name',
			    'colType'  => 'text',
				'colTitle' => '分組'
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
		$this->view->navArray = array('title' => '用戶列表');
		$this->view->itemName = '用戶';
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
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$table = new Users();
		$query = $table->getAllQuery( $where,$order_spec);

		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($currentPageNumber)
				  ->setItemCountPerPage($pageSize)
        		  ->setPageRange($pageRange);

		$this->view->paginator = $paginator;
		$this->view->count = $paginator->getCurrentItemCount();
		$groups = new Groups();
		$this->view->groupItems = $groups->getGroupsBySite(1);
		$this->dataList();
		
		//get birthday notice
		$this->view->birthdayUsers = $table->getBirthdayUsers(10);
	}

	public function editAction()
	{
		$this->view->navArray = array(
		                              '用戶列表'=>$this->view->url(array('controller'=>'users','module'=>'admin'),null,true),
		                              'title' => '添加新用戶'
		                             );
		$this->view->itemName = '用戶';
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
			$table = new Users();
			$row = $table->getUserById( $id );
			$this->view->form = $form = new UserEditForm();
			$form->setDefaults( $row );
			$this->view->navArray['title']= "修改用戶信息";
		}
		else{
			$this->view->form = $form = new UserAddForm();
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
		$table = new Users();
		$exist = $table->checkUserExist($form->getValue('username'),$form->getValue('user_email'));
		if($id){
			if( count($exist)>1 ) {
				$this->view->notice = "用戶名 或 郵箱 已經被使用";
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
				$this->view->notice = "用户名 或 邮箱 已经被使用";
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
		                              '用戶列表'=>$this->view->url(array('controller'=>'users','module'=>'admin'),null,true),
		                              'title' => '刪除用戶'
		                             );
		$this->view->itemName = '用戶户';
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
		$table = new Users();
		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->delete("user_id = '$id'");
				if($result){
					$this->_redirect('/admin/users');
				}
				else{
					$this->view->notice = "刪除失敗 ";
				}
			}
			else{
				$this->_redirect('/admin/users');
			}
		}
		$row = $table->getUserById( $id );
		$this->view->deleteName = $row['username'];
	}

	public function changegroupAction()
	{
	    $ids = $this->_request->getParam('idsToDelete',null);
	    $group = $this->_request->getParam('user_group',null);
	    if(!$ids || !$group) {
	        $this->_redirect("/admin/".$this->_request->getControllerName());
	        return;
	    }
	    $where = null;
	    $table = new Users();
	    if(is_array($ids)) {
	        foreach ($ids as $id) {
	            if($where) 
	                $where .= $table->getAdapter()->quoteInto(' OR user_id = ?', $id);
	            else
	                $where .= $table->getAdapter()->quoteInto('user_id = ?', $id);
	        }
	    }
	    else {
	        $where = $table->getAdapter()->quoteInto('user_id = ?', $ids);
	    }
	    $result = $table->updateGroup($group,$where);

	    $this->_redirect("/admin/".$this->_request->getControllerName());
	}

}