<?php
class Form_MooreForm extends Zend_Form
{


    public function myDecorators()
    {
        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'ul', 'class' => 'data-narrow',)),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
        $this->setElementDecorators(array(
            array('ViewHelper'),
     //       array('Errors'),
            array('Description',array('tag'=>'span','escape'=>false)),
            array('Label',array('separator'=>' ','tag'=>'span','escape'=>false)),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group'))

            ),
            array( 'thumbnail','logo','submit','myfile'),
            false
        );


    }

}