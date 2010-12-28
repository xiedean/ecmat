<?php
class Validate_EmailAddress extends Zend_Validate_EmailAddress 
{
    public function __construct( $allow = Zend_Validate_Hostname::ALLOW_DNS, $validateMx = false, Zend_Validate_Hostname $hostnameValidator = null )
    {
        parent::__construct( $allow, $validateMx, $hostnameValidator );
        $this->setMessages(array(
                            'emailAddressInvalid'=>'%value% 邮箱格式不对',
                            'emailAddressInvalidHostname'=>'%value% 邮箱格式不对',
                            'emailAddressInvalidMxRecord'=>'%value% 邮箱格式不对',
                            'emailAddressDotAtom'=>'%value% 邮箱格式不对',
                            'emailAddressQuotedString'=>'%value% 邮箱格式不对',
                            'emailAddressInvalidLocalPart'=>'%value% 邮箱格式不对',
                        ));
    }
}
