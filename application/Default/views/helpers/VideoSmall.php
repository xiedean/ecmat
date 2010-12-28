<?php
class Zend_View_Helper_VideoSmall
{
	/**
	 * get the baseUrl of the site
	 * 
	 * @param string $video
	 * @return string
	 */
	
	function videoSmall( $video )
	{
		$video = preg_replace("/(width=[\"\'])(.*?)([\"\'])/",'\1 210\3',$video);
		
		$video = preg_replace("/(height=[\"\'])(.*?)([\"\'])/",'\1 168\3',$video);

		return $video;
	}
}
?>