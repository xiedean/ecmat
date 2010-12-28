<?php
class Validate_StringLength extends Zend_Validate_StringLength 
{
    public function __construct( $min = 0, $max = null)
    {
        parent::__construct( $min, $max );
        $this->setMessages( array(
                          'stringLengthTooShort' => "'%value%' 长度不能小于  %min% ",
                          'stringLengthTooLong' => "'%value%' 长度不能大于 %max% "
                          ));
    }
}
