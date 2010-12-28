<?php
class Validate_Int extends Zend_Validate_Int 
{
    public function __construct()
    {
        $this->setMessage("'%value%' 不是整数",'notInt');
    }
}
