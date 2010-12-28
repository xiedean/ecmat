<?php

class AlbumnEditForm extends Form_MooreForm
{
    public function init()
    {

        $this->setAction("")
		->setMethod("POST")
		->setAttrib('id', 'albumnForm')	;


		$name = new Zend_Form_Element_Text(
            'albumn_name',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,255) ),
                'required'     => true,
                'class'        => 'form2',
                'label'        => '相冊名稱称'
            )
        );
        $status = new Zend_Form_Element_Radio(
            'status',
            array(
                'filters' => array('StringTrim'),
                'validators' => array( new Validate_StringLength(1,200) ),
                'required' => false,
                'label' => '狀態'
            )
        );
        $status->addMultiOptions(array('1' => '公開', '0' => '不公開' ) )
                  ->setSeparator('')
                  ->setValue('1');

        $submit = new Form_Element_Submit('修改','submit',false);

        $this->addElements( array($name, $status, $submit) );

        parent::myDecorators();

        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
    }
}
