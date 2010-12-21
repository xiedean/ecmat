<?php
class Moore_ErrorsHandle
{
	/**
	 * 
	 * @param array $errors
	 * @return boolean true
	 */
	public static function WriteDown( $errors, $file, $line )
	{
		//write the error to log file
		$registry = Zend_Registry::getInstance();
        $log = $registry->get("rootPath") ."\errors.log";
        $old = '';
        $str =  "";
        if( file_exists($log) ){
        	$old = file( $log );
        	$str = implode( "",$old);
        }
        $f = fopen( $log, "w" );
        $new = date("Y-m-d H:i:s")." FILE: " . $file . "; LINE: ".$line."; ".serialize($errors);

        $str .= "  \r\n  ".$new;
        fwrite( $f, $str );
        fclose( $f );
        return true;
	}
}