<?php

class FileEditForm extends Form_MooreForm
{
    public function init()
    {

        $this->setAction("")
		->setMethod("POST")
		->setAttrib('id', 'fileForm')
		->setAttrib('enctype', 'multipart/form-data');

		$registy = Zend_Registry::getInstance();
        $uploadPath = $registy->get('rootPath').$registy->get('uploadPath');
        $fileTypes = $registy->get('fileTypes');

		$name = new Zend_Form_Element_Text(
            'file_name',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,2000) ),
                'required'     => true,
                'class'        => 'form2',
                'label'        => '文件名稱'
            )
        );
        $file_src = new Zend_Form_Element_Text(
            'file_name_src',
            array(
                'validators'   => array( new Validate_StringLength(1,2000) ),
                'required'     => true,
                'class'        => 'form2',
                'label'        => '當前文件',
                'description'  => "fileDescription",
                'readonly'     => true
            )
        );


        $file = new Zend_Form_Element_File('myfile');
        $file->setLabel('上傳文件')
             ->setDestination( $uploadPath )
             ->setRequired(false)
             ->addValidator('Size', false, 2048000)
             ->addValidator('Count', false,1)
             ->addValidator('Extension', false, $fileTypes)
             ->setMaxFileSize(2048000)
             ->setValueDisabled(true);

        $site = new Zend_Form_Element_Select(
            'class',
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
        $status->addMultiOptions(array('1' => '開放', '0' => '不開放' ) )
                  ->setSeparator('')
                  ->setValue('1');

        $submit = new Form_Element_Submit('修改','submit',false);

        $this->addElements( array($name, $file_src, $file, $site, $status, $submit) );

        parent::myDecorators();

        $file->setDecorators(array(
            array('File'),
            array('Description',array('tag'=>'span')),
            array('Label',array('separator'=>' ')),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group'))


        ));

        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
    }

    public function addSite( $site_id )
    {
    	$site = new Zend_Form_Element_select(
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
