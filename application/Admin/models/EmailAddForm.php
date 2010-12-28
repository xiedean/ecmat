<?php

class EmailAddForm extends Form_MooreForm
{
    public function init()
    {

        $this->setAction("")
		->setMethod("POST")
		->setAttrib('id', 'emailForm')	;

        $to = new Zend_Form_Element_Select(
            'email_to',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => true,
                'class'        => 'form3',
                'label'        => '收件小組'
            )
        );
        $table = new Groups();
        $arr = $table->getGroups();
        $newArr = array();
        foreach( $arr as $a ){
        	$newArr += array($a['group_id']=>$a['group_name']);
        }
        $to->setMultiOptions($newArr);
        
        $from = new Zend_Form_Element_Text(
            'email_from',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,20000) ),
                'required'     => true,
                'class'        => 'form2',
                'value'        => Zend_Registry::get('siteEmail'),
                'label'        => '發件人'
            )
        );
        
        $title = new Zend_Form_Element_Text(
            'email_title',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,20000) ),
                'required'     => true,
                'class'        => 'form2',
                'label'        => '郵件標題'
            )
        );

        $content = new Zend_Form_Element_Textarea(
            'email_body',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1) ),
                'required'     => true,
                'class'        => 'form4',
                'label'        => '內容'
            )
        );
        
        $time = new Zend_Form_Element_Text(
            'created_time',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,20000) ),
                'required'     => true,
                'class'        => 'form2',
                'value'        => date("Y-m-d H:i:s"),
                'label'        => '時間'
            )
        );
 
        $submit = new Form_Element_Submit('發送','submit',false);

        $this->addElements( array($to, $from, $title, $content, $time,  $submit) );
        
        parent::myDecorators();

       
        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
    }



}
