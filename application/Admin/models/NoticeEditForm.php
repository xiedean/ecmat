<?php

class NoticeEditForm extends Form_MooreForm
{
    public function init()
    {
       $this->setAction("")
		->setMethod("POST")
		->setAttrib('id', 'noticeForm')	;

        //get baseUrl
        $fc = Zend_Controller_Front::getInstance();
		$baseurl = $fc->getBaseUrl();

		$title = new Zend_Form_Element_Text(
            'title',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,20000) ),
                'required'     => true,
                'class'        => 'form3',
                'label'        => '標題'
            )
        );

        $auth = new Zend_Form_Element_Text(
            'author',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => true,
                'class'        => 'form1',
                'label'        => '發布者'
            )
        );

        $time_modify = new Zend_Form_Element_Text(
            'modify_time',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => false,
                'class'        => 'form1',
                'value'        => date("Y-m-d H:i:s"),
                'label'        => '發布時間'
            )
        );

        $belong = new Zend_Form_Element_Select(
            'belong',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => true,
                'class'        => 'form3',
                'label'        => '所屬欄目'
            )
        );
        $arr = array('1'=>'活動公告','2'=>'今日看板','3'=>'明日活動');
        $belong->setMultiOptions($arr);

        $status = new Zend_Form_Element_Radio(
            'status',
            array(
                'filters' => array('StringTrim'),
                'validators' => array( new Validate_StringLength(1,200) ),
                'required' => false,
                'label' => '狀態'
            )
        );
        $status->addMultiOptions(array('1' => '已發布', '0' => '未發布' ) )
                  ->setSeparator('')
                  ->setValue('1');

        $submit = new Form_Element_Submit('修改','submit',false);

        $this->addElements( array($title, $auth, $time_modify, $belong, $status, $submit) );

        parent::myDecorators();


        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
    }



}
