<?php

require_once 'Zend/Form/Element/Password.php';

class Form_Element_Password extends Zend_Form_Element_Password
{
	/**
	 * Constructor
     *
 	 * @param  string $label 
 	 * @param  string $name
 	 * @param  string $required
	 * @return void
	 * @throws Zend_Form_Exception if no element name after initialization
	 */	
	public function __construct($label, $name, $required)
	{
		if (!is_string($name)) {
			require_once 'Zend/Form/Exception.php';
			throw new Zend_Form_Exception(get_class($this) . ' requires each element to have a name');
		}
		parent::__construct(
			$name, 
			array('filters'    => array('StringTrim'),
				  'validators' => array( new Validate_StringLength(1,20) ),
				  'required'   => $required,
				  'label'      => $label,
			      'class'      => 'form1'
			)
		);
	}
}