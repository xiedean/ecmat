<?php

class LinkAddForm extends Form_MooreForm
{
    public function init()
    {
       $this->setAction("")
		->setMethod("POST")
		->setAttrib('id', 'linkForm')	;

        //get baseUrl
//        $fc = Zend_Controller_Front::getInstance();
//		$baseurl = $fc->getBaseUrl();

		$name = new Zend_Form_Element_Text(
            'name',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,20000) ),
                'required'     => true,
                'class'        => 'form2',
                'label'        => '鏈接名稱'
            )
        );

        $url = new Zend_Form_Element_Text(
            'url',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,20000) ),
                'required'     => true,
                'class'        => 'form2',
                'label'        => '鏈接 URL'
            )
        );

        $site = new Zend_Form_Element_Select(
            'belong',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => true,
                'class'        => 'form3',
                'label'        => '所屬網站'
            )
        );
        $sites = new Sites();
        $ss = $sites->getSites();
        $newSites = array();
        foreach($ss as $s){
            $newSites += array($s['site_id']=>$s['site_name']);
        }
        $site->setMultiOptions($newSites);

        $status = new Zend_Form_Element_Radio(
            'status',
            array(
                'filters' => array('StringTrim'),
                'validators' => array( new Validate_StringLength(1,200) ),
                'required' => false,
                'label' => '狀態'
            )
        );
        $status->addMultiOptions(array('1' => '激活', '0' => '未激活' ) )
                  ->setSeparator('')
                  ->setValue('1');

        $submit = new Form_Element_Submit('添加','submit',false);

        $this->addElements( array($name, $url, $site, $status, $submit) );

        parent::myDecorators();

        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
    }



}
