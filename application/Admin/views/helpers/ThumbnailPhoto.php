<?php
class Zend_View_Helper_ThumbnailPhoto
{
	/**
	 * get the baseUrl of the site
	 *
	 * @return string
	 */
	function thumbnailPhoto( $photoname )
	{
		
		return "thumbnail/".$photoname;
	}
}
?>