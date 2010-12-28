<?php
class Zend_View_Helper_AlbumnPhoto
{
	/**
	 * get the baseUrl of the site
	 *
	 * @return string
	 */
	function albumnPhoto( $id )
	{
		$photos = new Photos();
		if($photos->getPhotoOfAlbumn($id)){
			return "thumbnail/".$photos->getPhotoOfAlbumn($id);
		}
		else{
			return false;
		}
	}
}
?>