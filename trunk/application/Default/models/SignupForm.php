<?php

class SignupForm extends Form_MooreForm
{

    public function init()
    {

        $this->setAction("")
		->setMethod("POST")
		->setAttrib('id', 'signupForm')	;

		$loginName = new Form_Element_Username('用戶名<span class="red">*</span>', 'username', true);


		$name = new Form_Element_Name('真實姓名', 'name', false);

		$gender = new Zend_Form_Element_Select(
		    'gender',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(0,2) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '性別'
            )
		);
		$gender->setMultiOptions(array('0'=>'男','1'=>'女'));

		$birthday = new Zend_Form_Element_Text(
		    'birthday',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,200) ),
                'required'     => false,
                'class'        => 'form2',
                'description'  => '格式：yyyy-mm-dd',
                'label'        => '生日'
            )
		);
		$education = new Zend_Form_Element_Select(
		    'education',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(0,50) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '學歷'
            )
		);
		$education->setMultiOptions(array(''=>'請選擇','doctor'=>'博士或以上','master'=>'碩士','undergraduate'=>'本科','junior'=>'專科','other'=>'專科以下'));

		$profession = new Zend_Form_Element_Text(
		    'profession',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,255) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '職業'
            )
		);
		$position = new Zend_Form_Element_Text(
		    'position',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,255) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '職位'
            )
		);
		$company = new Zend_Form_Element_Text(
		    'company',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => false,
                'class'        => 'form2',
                'discription'  => '格式：yyyy-mm-dd',
                'label'        => '工作單位'
            )
		);
		$company_ownership = new Zend_Form_Element_Select(
		    'company_ownership',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(0,50) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '单位性质'
            )
		);
		$company_ownership->setMultiOptions(array(''=>'請選擇','gov'=>'政府部門','state_enterprises'=>'國家事業單位','transnational_corporations'=>'跨國公司','private_firms'=>'國內私營公司','other'=>'其他'));

		$company_size = new Zend_Form_Element_Select(
		    'company_size',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(0,50) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '單位規模'
            )
		);
		$company_size->setMultiOptions(array(''=>'請選擇','0'=>'50人以下','1'=>'51-100人','2'=>'101-300人','3'=>'301-500人','4'=>'501-1000人','5'=>'1000人以上'));

		$company_address = new Zend_Form_Element_Text(
		    'company_address',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '單位地址'
            )
		);

		$address = new Zend_Form_Element_Text(
		    'address',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '居住地址'
            )
		);

		$postcode = new Zend_Form_Element_Text(
		    'postcode',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(6,7) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '居住郵編'
            )
		);

		$fixed_telephone = new Zend_Form_Element_Text(
		    'fixed_telephone',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(7,20) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '固定電話'
            )
		);

	    $mobilephone = new Zend_Form_Element_Text(
		    'mobilephone',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(7,20) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '手機'
            )
		);

		$email = new Form_Element_Email('E-mail<span class="red">*</span>','user_email',true);

		$qq = new Zend_Form_Element_Text(
		    'qq',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(4,20) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => 'QQ'
            )
		);

		$msn = new Zend_Form_Element_Text(
		    'msn',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(2,20) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => 'MSN'
            )
		);
		$personal_homepage = new Zend_Form_Element_Text(
		    'personal_homepage',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(2,500) ),
                'required'     => false,
                'class'        => 'form2',
                'value'        => 'http://',
                'label'        => '個人主頁'
            )
		);
		$interest = new Zend_Form_Element_Text(
		    'interest',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(6,7) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '單位郵編'
            )
		);
		$introduce = new Zend_Form_Element_Textarea(
            'introduce',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,5000) ),
                'required'     => false,
                'class'        => 'form5',
			    'description' => '小於100字',
                'label'        => '個人介紹'
            )
        );

        $password = new Form_Element_Password('設置密碼<span class="red">*</span>','password',true);


        $password2 = new Form_Element_Password('確認密碼<span class="red">*</span>','password2',true);


        //get baseUrl
        $fc = Zend_Controller_Front::getInstance();
		$baseurl = $fc->getBaseUrl();

        $captcha = new Zend_Form_Element_Captcha(
            'captcha',
            array(
                'label' => "驗證碼<span class=\"red\">*</span>",
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


        $submit = new Form_Element_Submit('註冊', 'submit', false);

        $this->addElements( array($loginName, $name, $birthday, $education, $profession, $position, $company, $company_ownership, $company_size, $company_address, $interest, $address, $postcode, $fixed_telephone, $mobilephone, $email, $qq, $msn, $personal_homepage, $introduce, $password, $password2, $captcha, $submit) );


        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'ul', 'class' => 'data-signup',)),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
        $this->setElementDecorators(array(
            array('ViewHelper'),
     //       array('Errors'),
            array('Description',array('tag'=>'span','class'=>'gray')),
            array('Label',array('separator'=>' ','tag'=>'span','escape'=>false)),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group'))

            ),
            array( 'thumbnail','logo'),
            false
        );
        $birthday->setDecorators(array(
      //      array('Errors'),
            array('Label'),
            array('ViewHelper'),
            array('Description',array('tag'=>'div', 'class'=>'gray')),
            array('HtmlTag', array('tag' => 'div', 'class'=>'captcha-group')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group captcha-line1')),

        ));
        // captcha do not need view helper
        $captcha->setDecorators(array(
      //      array('Errors'),
            array('Label',array('escape'=>false)),
            array('Description',array('tag'=>'span','class'=>'gray')),
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