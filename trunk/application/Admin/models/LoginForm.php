<?php

class LoginForm extends Form_MooreForm
{

    public function init()
    {

        $this->setAction("")
		->setMethod("POST")
		->setAttrib('id', 'loginForm')	;

		$loginName = new Form_Element_Username('管理員', 'username', true);

        $password = new Form_Element_Password('密碼','password',true);

        //get baseUrl
        $fc = Zend_Controller_Front::getInstance();
		$baseurl = $fc->getBaseUrl();

        $captcha = new Zend_Form_Element_Captcha(
            'verificate_code',
            array(
                'label' => "驗證碼",
                'class' =>'form1',
                'captcha' => 'Image',
                'captchaOptions' => array (
                'captcha' => 'Image',
                'wordLen' => 4,
                'timeout' => 300,
                'font' => 'font/arial.ttf',
                'fontsize' => 16,
                'width' => 80,
                'height' => 45,
                'dotNoiseLevel' => 5,
                'lineNoiseLevel' => 0,
                'gcFreq' => 10,  //How frequently to execute garbage collection
                'expiration' => 10,  //How long to keep generated images
                'imgurl' => "$baseurl/images/captcha",
                 ),

        ));

        $submit = new Form_Element_Submit('登陸', 'submit', false);

        $this->addElements( array($loginName, $password,  $captcha, $submit) );


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

        // captcha do not need view helper
        $captcha->setDecorators(array(
      //      array('Errors'),
            array('Label'),
            array('Description'),
      //      array('HtmlTag', array('tag' => 'div', 'class'=>'captcha-group')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group captcha-line1')),

        ));

        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
    }



}