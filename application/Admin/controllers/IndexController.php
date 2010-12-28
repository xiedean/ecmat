<?php
class Admin_IndexController extends Main_AdminController
{
/**
	 * All action of the Controller will do it
	 *
	 */
	function preDispatch()
	{

	}

	function indexAction()
	{

	}
	/**
	 * change my info
	 *
	 */
	public function editAction()
	{
		$this->view->navArray = array(
		                              '管理員列表'=>$this->view->url(array('controller'=>'admins','module'=>'admin'),null,true),
		                              'title' => '修改我的信息'
		                             );

		$id = $this->loggedInUser->user_id;
		if($id){
			$table = new Admins();
			$row = $table->getUserById( $id );
			$this->view->form = $form = new AdminEditForm();
			$form->setRoles( array('administrator'=> 'Administrator', 'editor'=>'Editor'));
			$form->setDefaults( $row );

		}
		else{
			$this->view->form = $form = new AdminAddForm();
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
		$data += array('role' => 'administrator');
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
			if($exist){
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
}
