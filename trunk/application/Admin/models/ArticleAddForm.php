<?php

class ArticleAddForm extends Form_MooreForm
{
    protected $_site_id = 1;

    public function __construct( $site_id=1 )
    {
        $this->_site_id = $site_id;
        parent::__construct();
    }
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
        
        ini_set('max_execution_time',0);

        $this->setAction("")
		->setMethod("POST")
		->setAttrib('id', 'articleForm')	;


		$class = new Zend_Form_Element_Select(
            'class_id',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => true,
                'class'        => 'form3',
                'label'        => '所屬欄目'
            )
        );
		$subtitle = new Zend_Form_Element_Text(
            'subtitle',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,20000) ),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '熱點議題標題'
            )
        );
        $title = new Zend_Form_Element_Text(
            'title',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,20000) ),
                'required'     => true,
                'class'        => 'form2',
                'label'        => '標題'
            )
        );
        $auth = new Zend_Form_Element_Text(
            'author',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => true,
                'class'        => 'form1',
                'label'        => '作者'
            )
        );

        
        $classes = new Classes();
        $arrayCla = $classes->getClassesBySite( $this->_site_id );
        $newCla = array();
        foreach( $arrayCla as $cl ){
        	$newCla += array($cl['class_id']=>$cl['class_name']);
        }
        $class->setMultiOptions($newCla);

        $focus = new Zend_Form_Element_Checkbox(
            'focus',
            array(
                'label' => '焦點資訊？',
                'checkedValue' => 1
                
                )
        );
		$activity = new Zend_Form_Element_Checkbox(
            'is_activity',
            array(
                'label' => '歷年活動？',
                'checkedValue' => 1
                
                )
        );
         
        $time_created = new Zend_Form_Element_Hidden(
            'created',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => false,
                'class'        => 'form1',
                'value'        => date("Y-m-d H:i:s")
            )
        );

        $time_modify = new Zend_Form_Element_Text(
            'modify_time',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1,500) ),
                'required'     => false,
                'class'        => 'form1',
                'value'        => date("Y-m-d H:i:s"),
                'label'        => '發佈時間'
            )
        );

        $content = new Zend_Form_Element_Textarea(
            'content',
            array(
                'filters'      => array('StringTrim'),
                'validators'   => array( new Validate_StringLength(1) ),
                'required'     => true,
                'class'        => 'form4',
                'label'        => '內容'
            )
        );

        $image = new Zend_Form_Element_File('news_image');
        $registy = new Zend_Registry();
        $imageUploadPath = $registy->get('rootPath').$registy->get('newsImagePath');
        $fileTypes = array('jpg','gif','png','jpeg','bmp');
        $image->setLabel('上傳新聞圖片')
             ->setDescription("圖片寬度建議為 200像素, 圖片小於 2M")
             ->setDestination( $imageUploadPath )
             ->setRequired(false)
             ->addValidator('Size', false, 2048000)
             ->addValidator('Count', true,1)
             ->addValidator('Extension', false, $fileTypes)
             ->setMaxFileSize(2048000)
             ->setValueDisabled(true);
             
        $video = new Zend_Form_Element_File('news_video');
        $videoType = array('flv');
        $videoSize = 2048000*4;
        $videoUploadPath = $registy->get('rootPath').$registy->get('newsVideoPath');
        $video->setLabel('上傳視頻')
            ->setDescription('視頻格式 flv, 最大 8M')
            ->setDestination($videoUploadPath)
            ->setRequired(false)
            ->addValidator('Size',false,$videoSize)
            ->addValidator('Count',1)
            ->addValidator('Extension',false,$videoType)
            ->setMaxFileSize($videoSize)
            ->setValueDisabled(true);

        $video_embed = new Zend_Form_Element_Text(
        	'video_embed',
            array(
                'filters'      => array('StringTrim'),
                'required'     => false,
                'class'        => 'form2',
                'label'        => '嵌入視頻'
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
        $status->addMultiOptions(array('1' => '已發布', '0' => '未發表' ) )
                  ->setSeparator('')
                  ->setValue('1');

        $submit = new Form_Element_Submit('添加','submit',false);

        if($this->_site_id == '1') {
            $this->addElements( array($class, $subtitle, $title, $auth, $focus, $activity, $time_created, $time_modify, $content, $image, $video_embed, $status, $submit) );
        }else {
            $this->addElements( array($title, $auth, $class, $time_created, $time_modify, $content, $image, $video_embed, $status, $submit) );
        }
        
        parent::myDecorators();

        $time_created->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'hidden')),
            array(array('row' => 'HtmlTag'), array('tag' => 'div','class' => 'hidden')),
        ));

        $image->setDecorators(array(
            array('File'),
            array('Description',array('tag'=>'span')),
            array('Label',array('separator'=>' ')),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group'))


        ));
        
        $video->setDecorators(array(
            array('File'),
            array('Description',array('tag'=>'span')),
            array('Label',array('separator'=>' ')),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group'))


        ));
        
        $subtitle->setDecorators(array(
            array('ViewHelper'),
     //       array('Errors'),
            array('Description',array('tag'=>'span','escape'=>false)),
            array('Label',array('separator'=>' ','tag'=>'span','escape'=>false)),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group hidden'))

            )
        );
        
        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
    }



}
