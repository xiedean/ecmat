<?php
class Zend_View_Helper_ImagePath
{
	/**
	 * get the imagePath of the site
	 *
	 * @return string
	 */
	function imagePath()
	{
	    $registy = new Zend_Registry();
		return $registy->get('newsImagePath');
	}
}
?>