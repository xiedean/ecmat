<?php
class Zend_View_Helper_VideoPath
{
	/**
	 * get the videoPath of the site
	 *
	 * @return string
	 */
	function videoPath()
	{
	    $registy = new Zend_Registry();
		return $registy->get('newsVideoPath');
	}
}
?>