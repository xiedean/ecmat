<?php
class Validate_NotEmpty extends Zend_Validate_NotEmpty 
{
    public function __construct()
    {
        $this->setMessage('这里不能为空','isEmpty');
    }
}
