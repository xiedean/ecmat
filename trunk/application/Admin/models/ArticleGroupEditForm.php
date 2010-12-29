<?php

class ArticleGroupEditForm extends Form_MooreForm  
{
    
    public function init()
    {   
        $this->setAction("")
		->setMethod("POST")	
		->setAttrib('id', 'classForm')	;

		$name = new Zend_Form_Element_Text(
            'article_group_name',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,2000) ),
                'required'     => true,
                'class'        => 'form2',
                'label'        => '文章組名稱'
            )
        );
        

        
        $submit = new Form_Element_Submit('修改','submit',false);
        
        $this->addElements( array($name, $submit) );

        parent::myDecorators();
        
        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'), 
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),
            
        ));
    }
    
    
    
}
