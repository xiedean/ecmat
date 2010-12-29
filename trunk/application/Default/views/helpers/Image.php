<?php
class Zend_View_Helper_Image
{
    public $view;
	
	protected $_image;
	protected $_imageName;
	protected $_dir;
	
	const IMAGE_CACHE_PATH = 'cache';

	/**
	 * 
	 * @param string $image
	 * @return Zend_View_Helper_Image $this
	 */
    public function image ($image, $dir=null)
    {
		if(!$dir) {
			$dir = $this->getDefaultDir();
		}
		$this->_imageName = $image;
        $this->_image = $dir.$image;
		$this->_dir = $dir;
		
		return $this;
    }
	
	public function resize($width, $height=null)
	{
		$imageName = $width."x".$height."_".$this->_imageName;
		$path = $this->getRootPath().$this->view->imagePath();
		$cachePath = $path.self::IMAGE_CACHE_PATH . DIRECTORY_SEPARATOR;
		if(!is_dir($cachePath)) {
			mkdir($cachePath,0777);
		}
		$file = $this->getRootPath().$this->view->imagePath().$this->_imageName;
		if(!is_file($this->getRootPath().$this->view->imagePath() . self::IMAGE_CACHE_PATH . DIRECTORY_SEPARATOR . $imageName)) {
			$this->resizeTo($file, $imageName, $cachePath, $width, $height);
		}
		$this->_image = $this->_dir . self::IMAGE_CACHE_PATH . "/" . $imageName;

		return $this;
	}
	
	public function setImage($image)
	{
		$this->_image = $image;
	}
	
	public function getImage()
	{
		return $this->_image;
	}
	
	public function getDefaultDir()
	{
		return $this->view->baseUrl().$this->view->imagePath();;
	}
	
	public function __toString()
	{
		return $this->_image;
	}
	
	public function getRootPath()
	{
		$registy = new Zend_Registry();
		return $registy->get('rootPath');
	}
	
	function resizeTo($filename, $imageName, $to, $width, $height=null)
	{
		$size = getimagesize($filename);
		$_width = $size[0];
		$_height = $size[1];
		$k0 = $_width / $_height;
		if(!$height) {
			$height = $width;
		}
		if($width > $_width && $height > $_height) {
			$width = $_width;
			$height = $_height;
		}
		if($width > $height) {
			$width = $k0 * $height;
		} else {
			$height = $width / $k0;
		}
		$image_p = imagecreatetruecolor($width, $height);
		switch($size[2])
		{
			case 1:
				$func = 'imagecreatefromgif';
				$func2 = 'imagegif';
				break;
			case 2:
				$func = 'imagecreatefromjpeg';
				$func2 = 'imagejpeg';
				break;
			case 3:
				$func = 'imagecreatefrompng';
				$func2 = 'imagepng';
				break;
		}
		$image = $func($filename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
		if($size[2] ==2) {
			$func2($image_p,$to.$imageName,100);
		} else {
			$func2($image_p,$to.$imageName);
		}
	}


    /**
	 * Sets the view field
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}

}