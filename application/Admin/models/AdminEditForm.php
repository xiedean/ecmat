<?php

class AdminEditForm extends Form_MooreForm
{
    protected $_roles = array('user'=>'User');

    public function setRoles( $arr )
    {
    	$this->_roles = $arr;
    	$this->init();
    }

    public function getRoles()
    {
    	return $this->_roles;
    }

    public function init()
    {
        $this->setAction("")
		->setMethod("POST")
		->setAttrib('id', 'signupForm')	;

        //get baseUrl
        $fc = Zend_Controller_Front::getInstance();
		$baseurl = $fc->getBaseUrl();

		$loginName = new Form_Element_Username('用戶名*', 'username', true);

        $password = new Form_Element_Password('密碼','password',false);
        $password->setDescription('如果不改變密碼，"密碼" 欄 和 "確認密碼" 欄請留空');

        $password2 = new Form_Element_Password('確認密碼','password2',false);

		$email = new Form_Element_Email('郵箱*','user_email',true);

        $status = new Zend_Form_Element_Radio(
            'status',
            array(
                'filters' => array('StringTrim'),
                'validators' => array( new Validate_StringLength(1,200) ),
                'required' => false,
                'label' => '狀態'
            )
        );
        $status->addMultiOptions(array('1' => '已激活', '0' => '未激活' ) )
                  ->setSeparator('')
                  ->setValue('1');

        $submit = new Form_Element_Submit('修改','submit',false);

        $this->addElements( array($loginName, $email, $password, $password2,$status, $submit) );

        parent::myDecorators();

        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
       
    }


}

