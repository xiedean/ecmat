<?php

class PasswordForm extends Form_MooreForm  
{
    
    public function init()
    {   
        
        $this->setAction("")
		->setMethod("POST")	
		->setAttrib('id', 'passwordForm')	;

		$op = new Form_Element_Password('舊密碼', 'password_old', true);
        $np = new Form_Element_Password('新密碼','password_new',true);
        $npc = new Form_Element_Password('確認新密碼','password_con',true);
        
        

        $submit = new Form_Element_Submit('修改', 'submit', false);
       
        $this->addElements( array($op, $np, $npc, $submit) );
       

        parent::myDecorators();
        
         
        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'), 
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),
            
        ));
    }
    

    
}