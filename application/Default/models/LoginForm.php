<?php

class LoginForm extends Form_MooreForm
{

    public function init()
    {
        $fc = Zend_Controller_Front::getInstance();
        $baseurl = $fc->getBaseUrl();

        $this->setAction("$baseurl/account/login")
		     ->setMethod("POST")
		     ->setAttrib('id', 'loginForm')	;

		$loginName = new Form_Element_Username('用戶名', 'username', true);

        $password = new Form_Element_Password('密碼','password',true);

        $submit = new Form_Element_Submit('登陸', 'submit', false);

        $this->addElements( array($loginName, $password,  $submit) );


        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'ul', 'class' => 'data-login',)),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
        $this->setElementDecorators(array(
            array('ViewHelper'),
     //       array('Errors'),
            array('Description'),
            array('Label',array('separator'=>' ')),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group'))

            ),
            array( 'thumbnail','logo'),
            false
        );


        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
    }



}