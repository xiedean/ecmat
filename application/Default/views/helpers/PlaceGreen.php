<?php
class Zend_View_Helper_PlaceGreen
{
	/**
	 * get the baseUrl of the site
	 *
	 * @return string
	 */
	function placeGreen( $str )
	{
		$str = str_replace("[place]","<span class=\"green\">",$str);
		$str = str_replace("[/place]","</span>",$str);
		return $str;
	}
}
?>