<?php

class ArticlePageAddForm extends Form_MooreForm
{
    protected $_site_id = 1;

    public function setSiteId( $id )
    {
    	$this->_site_id = $id;
    	$this->init();
    }

    public function getSiteId()
    {
    	return $this->_site_id;
    }

    public function init()
    {

        $this->setAction("")
		->setMethod("POST")
		->setAttrib('id', 'articlepageForm')	;

        $page = new Zend_Form_Element_Text(
            'article_page',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => true,
                'class'        => 'form1',
                'label'        => '頁碼'
            )
        );



        $content = new Zend_Form_Element_Textarea(
            'page_content',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1) ),
                'required'     => true,
                'class'        => 'form4',
                'label'        => '內容'
            )
        );


        $submit = new Form_Element_Submit('發布','submit',false);

        $this->addElements( array($page, $content, $submit) );

        parent::myDecorators();

        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
    }



}
