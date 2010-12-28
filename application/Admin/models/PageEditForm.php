<?php

class PageEditForm extends Form_MooreForm  
{
    public function init()
    {   
       $this->setAction("")
		->setMethod("POST")	
		->setAttrib('id', 'pageForm')	;

		$title = new Zend_Form_Element_Text(
            'page_title',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,2000) ),
                'required'     => true,
                'class'        => 'form2',
                'label'        => '頁面標題'
            )
        );
        
        
        $content = new Zend_Form_Element_Textarea(
            'content',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,2000000) ),
                'required'     => true,
                'class'        => 'form4',
                'label'        => '內容'
            )
        );
        
        $submit = new Form_Element_Submit('修改','submit',false);
        
        $this->addElements( array($title, $content, $submit) );

        parent::myDecorators();
        
        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'), 
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),
            
        ));
    }
    

    
}
