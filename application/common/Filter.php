<?php
class Filter
{
	protected $_filters;
	protected $_validators;
	protected $_errors;

	public function Filter()
	{
		// filter input
		$this->_filters = array(
		    	'*'  => 'StringTrim'
			);
		$this->_validators = array(
	    		'keyword' => new Zend_Validate_Regex('/^[\s\S ]*$/'),
		    	'by' => new Zend_Validate_Regex('/^[0-9a-zA-Z_]+$/'),
	    		'order'=> new Zend_Validate_Regex('/^[0-9a-zA-Z_]+$/'),
	    		'to' => 'Alpha',
	    		'page' => 'Digits',
	    		'id' => 'Digits',
		        'role' => 'Alpha',
		        '*'    => array(new Zend_Validate_Regex('/^[\s\S ]+$/'), 'allowEmpty' => true)
			);
	}

	public function filterValid($params)
	{

		$input = new Zend_Filter_Input($this->_filters, $this->_validators, $params);
		if($input->isValid()){
			return $input;
		}else {
			$this->_errors = $input->getMessages();
			return false;
		}
	}

	public function getErrors()
	{
		return $this->_errors;
	}
}
