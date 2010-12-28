<?php

class ClassEditForm extends Form_MooreForm  
{
    
    public function init()
    {   
        $this->setAction("")
		->setMethod("POST")	
		->setAttrib('id', 'classForm')	;

		$name = new Zend_Form_Element_Text(
            'class_name',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,2000) ),
                'required'     => true,
                'class'        => 'form2',
                'label'        => '欄目名稱'
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
    
    public function addParent( $site_id, $id )
    {
    	if( !($site_id && $id) ){
    		return false;
    	}
    	$parent = new Zend_Form_Element_Select(
                'parent',
                array(
                    'filters'      => array('StringTrim'),
                    'validators'   => array( new Validate_StringLength(1,500) ),
                    'required'     => false,
                    'class'        => 'form3',
                    'order'        => 2,
                    'label'        => '父級欄目'
                )
            );
        $classes = new Classes();
        $array_class = $classes->getClassesBySite( $site_id );
        if($array_class){
                $new_class = array(0=>'root');
                foreach( $array_class as $c ){
                	if($c['class_id']!= $id)
            	        $new_class += array($c['class_id']=>$c['class_name']);
                }
        }
        $parent->setMultiOptions( $new_class );
        $this->addElements(array($parent));
            
        $parent->setDecorators(array(
            array('ViewHelper'),
            array('Description',array('tag'=>'span')),
            array('Label',array('separator'=>' ')),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group'))
            
            )
        );
            return $this;
    }
    
    public function addSite( $site_id )
    {
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
        $s = $sites->getSiteById( $site_id );
        $newSites = array($s['site_id']=>$s['site_name']);
        $site->setMultiOptions($newSites);
        $this->addElements(array($site));
        
        $site->setDecorators(array(
            array('ViewHelper'),
            array('Description',array('tag'=>'span')),
            array('Label',array('separator'=>' ')),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group'))
            
            )
        );
        return $this;
    }

    
}
