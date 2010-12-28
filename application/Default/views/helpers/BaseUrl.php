<?php
class Zend_View_Helper_BaseUrl
{
	/**
	 * get the baseUrl of the site
	 *
	 * @return string
	 */
	function baseUrl()
	{
		$fc = Zend_Controller_Front::getInstance();
		$baseurl = $fc->getBaseUrl();
		$baseurl = str_replace('/index.php','',$baseurl);
		return $baseurl;
	}
}
?>