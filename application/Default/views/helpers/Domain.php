<?php
class Zend_View_Helper_Domain
{
	/**
	 * get the domain of the site
	 *
	 * @return string
	 */
	function domain()
	{
		//return "http://".$_SERVER['HTTP_HOST'].'/mpro';
		return "http://".$_SERVER['HTTP_HOST'];
	}
}
?>