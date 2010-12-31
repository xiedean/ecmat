<?php

class UserEditForm extends Form_MooreForm
{
    public function init()
    {
        $this->setAction("")
		->setMethod("POST")
		->setAttrib('id', 'signupForm')	;

        //get baseUrl
        $fc = Zend_Controller_Front::getInstance();
		$baseurl = $fc->getBaseUrl();

		$loginName = new Form_Element_Username('用戶名*', 'username', true);
		$loginName->setAttribs(array('readonly'=>true));

        $password = new Form_Element_Password('密碼','password',false);
        $password->setDescription('如果不改變密碼，"密碼" 欄 和 "確認密碼" 欄請留空');

        $password2 = new Form_Element_Password('確認密碼','password2',false);


		$name = new Form_Element_Name('真實姓名', 'name', true);

		$gender = new Zend_Form_Element_Select(
		    'gender',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(0,2) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '性别'
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
                'label'        => '單位性質'
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
		$company_size->setMultiOptions(array('0'=>'50人以下','1'=>'51-100人','2'=>'101-300人','3'=>'301-500人','4'=>'501-1000人','5'=>'1000人以上'));

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
                'label'        => '郵政編碼'
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

		$email = new Form_Element_Email('郵箱*','user_email',true);
		$email->setAttribs(array('readonly'=>true));


		$qq = $fixed_telephone = new Zend_Form_Element_Text(
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
		$personal_homepage = $fixed_telephone = new Zend_Form_Element_Text(
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
		$interest = $fixed_telephone = new Zend_Form_Element_Text(
		    'interest',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '興趣愛好'
            )
		);
		$introduce = new Zend_Form_Element_Textarea(
            'introduce',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,5000) ),
                'required'     => false,
                'class'        => 'form5',
			    'description' => '小于100字',
                'label'        => '個人介紹'
            )
        );
        $submit = new Form_Element_Submit('修改','submit',false);

        $this->addElements( array($loginName, $name, $birthday, $education, $profession, $position, $company, $company_ownership, $company_size, $company_address, $address, $postcode, $fixed_telephone, $mobilephone, $email, $qq, $msn, $personal_homepage, $interest, $introduce, $password, $password2, $submit) );

        parent::myDecorators();

        $password->setDecorators(array(
            array('ViewHelper'),
            array('Description',array('tag'=>'div','class'=>'small')),
            array('Label',array('separator'=>' ')),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group'))

            ),
            array( 'thumbnail','logo','submit','myfile'),
            false
        );

        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
        $birthday->setDecorators(array(
      //      array('Errors'),
            array('Label'),
            array('ViewHelper'),
            array('Description',array('tag'=>'div', 'class'=>'gray')),
            array('HtmlTag', array('tag' => 'div', 'class'=>'captcha-group')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group captcha-line1')),

        ));
    }


}
