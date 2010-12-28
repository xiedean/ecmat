<?php

class GroupEditForm extends Form_MooreForm  
{
    
    public function init()
    {   
        $this->setAction("")
		->setMethod("POST")	
		->setAttrib('id', 'groupForm')	;

		$name = new Zend_Form_Element_Text(
            'group_name',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,2000) ),
                'required'     => true,
                'class'        => 'form2',
                'label'        => '分組名稱'
            )
        );
        
        $site = new Zend_Form_Element_Select(
            'belong',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => true,
                'class'        => 'form3',
                'order'        => 1,
                'label'        => '所屬網站'
            )
        );
        $sites = new Sites();
        $sArr = $sites->getSites();
        $newSites = array();
        foreach($sArr as $s){
            $newSites[$s['site_id']] = $s['site_name'];
        }
        $site->setMultiOptions($newSites);

        
        $submit = new Form_Element_Submit('修改','submit',false);
        
        $this->addElements( array($name, $site, $submit) );

        parent::myDecorators();
        
        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'), 
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),
            
        ));
    }
    
     
    

    
}
