<?php

class ForgotForm extends Form_MooreForm
{

    public function init()
    {

        $this->setAction("")
		     ->setMethod("POST")
		     ->setAttrib('id', 'forgotForm')	;


        $email = new Form_Element_Email('E-mail', 'email', true);

        $submit = new Form_Element_Submit('發送', 'submit', false);

        $this->addElements( array($email,$submit) );


        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'ul', 'class' => 'data-signup',)),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
        $this->setElementDecorators(array(
            array('ViewHelper'),
     //       array('Errors'),
            array('Description'),
            array('Label',array('separator'=>' ')),
            array('HtmlTag', array('tag' => 'li', 'class' => 'element-group'))

            ),
            array( 'thumbnail','logo'),
            false
        );


        // buttons do not need labels
        $submit->setDecorators(array(
            array('ViewHelper'),
            array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
            array(array('row' => 'HtmlTag'), array('tag' => 'li','class' => 'element-group')),

        ));
    }



}