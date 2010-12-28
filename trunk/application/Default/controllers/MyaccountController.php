<?php
class MyaccountController extends Main_CommonController
{
    public function indexAction()
    {

    }

    function editAction()
    {
        $this->view->headerName = "修改我的信息";
        $this->view->form = $form = new UserEditForm();
        $form->setDefaults(parent::formatData($this->loggedInUser));
        if( !$this->_request->isPost() ){
            return;
        }
        if($form->isValid($this->_request->getPost())){
            $errors = $form->getMessages();
            foreach($errors as $key=>$err){
				$this->view->notice = $form->getElement($key)->getLabel()." 格式不对";
				$this->view->errorId = $key;
				return;
			}
        }

        if($form->getValue('password') != $form->getValue('password2')){
            $this->view->notice = "两次密码不相同";
            $this->view->errorId = "password";
            return;
        }
        $data = parent::formatData($form->getValues());
        $table = new Users();
        $result = $table->update( $data,"user_id = '{$this->loggedInUser->user_id}'");
        if($result){
            $this->view->notice = "我的信息修改成功";
            $rows = $table->fetchRow("user_id = '{$this->loggedInUser->user_id}'");
            $rows->password = null;
            $this->loggedInUser = $rows;
        }
        else{
            $this->view->notice = "我的信息修改失败";
        }


    }
}