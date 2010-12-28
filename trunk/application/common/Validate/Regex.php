<?php
class Validate_Regex extends Zend_Validate_Regex 
{
    public function __construct( $pattern )
    {
        parent::__construct( $pattern );
        $this->setMessage("'%value%' 格式不正确",'regexNotMatch');
    }
}
