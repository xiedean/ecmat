<?php

class ContactForm extends Form_MooreForm
{

    public function init()
    {
        $fc = Zend_Controller_Front::getInstance();
        $baseurl = $fc->getBaseUrl();

        $this->setAction("")
		     ->setMethod("POST")
		     ->setAttrib('id', 'contactForm')	;

		$name = new Form_Element_Name('姓名', 'name', true);
		$name->setDescription('*');

		$email = new Form_Element_Email('E-mail','email',true);
		$email->setDescription('*');

		$title = new Zend_Form_Element_Text(
            'title',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,20000) ),
                'required'     => true,
                'class'        => 'form2',
			    'description' => '*',
                'label'        => '主題'
            )
        );
        $content = new Zend_Form_Element_Textarea(
            'content',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,2000000) ),
                'required'     => true,
                'class'        => 'form4',
			    'description' => '*',
                'label'        => '內容'
            )
        );

        //get baseUrl
        $fc = Zend_Controller_Front::getInstance();
		$baseurl = $fc->getBaseUrl();

        $captcha = new Zend_Form_Element_Captcha(
            'captcha',
            array(
                'label' => "驗證碼",
                'description' => '*',
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

        $submit = new Form_Element_Submit('發送', 'submit', false);

        $this->addElements( array($name, $email, $title, $content, $captcha, $submit) );


        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'ul', 'class' => 'data-narrow',)),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
        $this->setElementDecorators(array(
            array('ViewHelper'),
     //       array('Errors'),
            array('Description',array('tag'=>'span','class'=>'red')),
            array('Label',array('separator'=>' ')),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group'))

            ),
            array( 'thumbnail','logo'),
            false
        );

        $captcha->setDecorators(array(
      //      array('Errors'),
            array('Label'),
            array('Description',array('tag'=>'span','class'=>'red')),
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